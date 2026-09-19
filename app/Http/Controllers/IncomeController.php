<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
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

        $incomes = Income::with(['period.group', 'member', 'creator'])
            ->when($groupId, function ($q) use ($groupId) {
                $q->whereHas('period', fn ($p) => $p->where('group_id', $groupId));
            })
            ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
            ->orderBy('transaction_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalNominal = (float) Income::when($groupId, function ($q) use ($groupId) {
            $q->whereHas('period', fn ($p) => $p->where('group_id', $groupId));
        })
            ->when($periodId, fn ($q) => $q->where('period_id', $periodId))
            ->sum('nominal');

        return view('incomes.index', compact('incomes', 'groups', 'periods', 'groupId', 'periodId', 'totalNominal'));
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

        $members = Member::where('group_id', $selectedGroupId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $currentGroup = $groups->firstWhere('id', $selectedGroupId);
        $defaultDue = $currentGroup?->default_due_amount ?? 0;

        return view('incomes.create', compact('groups', 'periods', 'members', 'selectedGroupId', 'defaultDue'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id'],
            'member_id' => ['required', 'exists:members,id'],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        Income::create([
            'period_id' => $validated['period_id'],
            'member_id' => $validated['member_id'],
            'nominal' => $validated['nominal'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('incomes.index', ['period_id' => $validated['period_id']])
            ->with('success', 'Setoran kas berhasil dicatat.');
    }

    public function edit(Income $income): View
    {
        $groups = Group::orderBy('name')->get();
        $period = $income->period;
        $members = Member::where('group_id', $period->group_id)->orderBy('name')->get();
        $periods = Period::where('group_id', $period->group_id)->orderBy('year', 'desc')->orderBy('month', 'desc')->get();

        return view('incomes.edit', compact('income', 'groups', 'members', 'periods'));
    }

    public function update(Request $request, Income $income): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id'],
            'member_id' => ['required', 'exists:members,id'],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $income->update([
            'period_id' => $validated['period_id'],
            'member_id' => $validated['member_id'],
            'nominal' => $validated['nominal'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('incomes.index', ['period_id' => $validated['period_id']])
            ->with('success', 'Data setoran kas berhasil diperbarui.');
    }

    public function destroy(Income $income): RedirectResponse
    {
        $periodId = $income->period_id;
        $income->delete();

        return redirect()->route('incomes.index', ['period_id' => $periodId])
            ->with('success', 'Catatan setoran berhasil dihapus.');
    }
}
