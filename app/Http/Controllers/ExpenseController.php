<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseAttachment;
use App\Models\ExpenseCategory;
use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $periodId = $request->filled('period_id') ? (int) $request->period_id : null;

        $groups = Group::orderBy('name')->get();

        $periods = Period::when($groupId, fn ($q) => $q->where('group_id', $groupId))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $expenses = Expense::with(['period.group', 'category', 'creator', 'attachments'])
            ->when($groupId, function ($q) use ($groupId) {
                $q->whereHas('period', fn ($p) => $p->where('group_id', $groupId));
            })
            ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalNominal = (float) Expense::when($groupId, function ($q) use ($groupId) {
            $q->whereHas('period', fn ($p) => $p->where('group_id', $groupId));
        })
            ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
            ->sum('nominal');

        return view('expenses.index', compact('expenses', 'groups', 'periods', 'groupId', 'periodId', 'totalNominal'));
    }

    public function create(Request $request): View
    {
        $groups = Group::orderBy('name')->get();
        $selectedGroupId = $request->filled('group_id') ? (int) $request->group_id : $groups->first()?->id;

        $periods = Period::where('group_id', $selectedGroupId)
            ->whereIn('status', ['open', 'draft'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.create', compact('groups', 'periods', 'categories', 'selectedGroupId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id'],
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:5120'], // Max 5MB per file
        ]);

        $expense = Expense::create([
            'period_id' => $validated['period_id'],
            'category_id' => $validated['category_id'] ?? null,
            'item_name' => $validated['item_name'],
            'nominal' => $validated['nominal'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                ExpenseAttachment::create([
                    'expense_id' => $expense->id,
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()->route('expenses.index', ['period_id' => $validated['period_id']])
            ->with('success', 'Pengeluaran kas berhasil dicatat.');
    }

    public function edit(Expense $expense): View
    {
        $expense->load(['period', 'attachments']);
        $period = $expense->period;

        $periods = Period::where('group_id', $period->group_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'periods', 'categories'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id'],
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'mimes:jpeg,jpg,png,webp,pdf', 'max:5120'],
        ]);

        $expense->update([
            'period_id' => $validated['period_id'],
            'category_id' => $validated['category_id'] ?? null,
            'item_name' => $validated['item_name'],
            'nominal' => $validated['nominal'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                ExpenseAttachment::create([
                    'expense_id' => $expense->id,
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()->route('expenses.index', ['period_id' => $validated['period_id']])
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    public function destroyAttachment(ExpenseAttachment $attachment): RedirectResponse
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Lampiran bukti pengeluaran berhasil dihapus.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $periodId = $expense->period_id;

        foreach ($expense->attachments as $att) {
            if (Storage::disk('public')->exists($att->file_path)) {
                Storage::disk('public')->delete($att->file_path);
            }
        }

        $expense->delete();

        return redirect()->route('expenses.index', ['period_id' => $periodId])
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}
