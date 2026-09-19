<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeriodController extends Controller
{
    public function index(Request $request): View
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $groups = Group::orderBy('name')->get();

        $periods = Period::with(['group', 'creator'])
            ->withCount(['incomes', 'expenses'])
            ->when($groupId, fn ($q) => $q->where('group_id', $groupId))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('periods.index', compact('periods', 'groups', 'groupId'));
    }

    public function create(): View
    {
        $groups = Group::orderBy('name')->get();

        return view('periods.create', compact('groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'due_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ]);

        // Prevent duplicate period for the same group and month/year
        $exists = Period::where('group_id', $validated['group_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Periode untuk bulan dan tahun ini sudah ada pada grup tersebut.'])->withInput();
        }

        Period::create([
            'group_id' => $validated['group_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'due_amount' => $validated['due_amount'] !== null && $validated['due_amount'] !== '' ? $validated['due_amount'] : null,
            'status' => $validated['status'],
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('periods.index', ['group_id' => $validated['group_id']])
            ->with('success', 'Periode kas berhasil dibuat.');
    }

    public function edit(Period $period): View
    {
        $groups = Group::orderBy('name')->get();

        return view('periods.edit', compact('period', 'groups'));
    }

    public function update(Request $request, Period $period): RedirectResponse
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'due_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ]);

        $exists = Period::where('group_id', $validated['group_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $period->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Periode untuk bulan dan tahun ini sudah ada pada grup tersebut.'])->withInput();
        }

        $period->update([
            'group_id' => $validated['group_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'due_amount' => $validated['due_amount'] !== null && $validated['due_amount'] !== '' ? $validated['due_amount'] : null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('periods.index', ['group_id' => $period->group_id])
            ->with('success', 'Data periode berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Period $period): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'open', 'closed'])],
        ]);

        $period->update(['status' => $validated['status']]);

        return back()->with('success', "Status periode {$period->period_name} diubah menjadi {$period->status}.");
    }

    public function destroy(Period $period): RedirectResponse
    {
        $groupId = $period->group_id;
        $period->delete();

        return redirect()->route('periods.index', ['group_id' => $groupId])
            ->with('success', 'Periode kas berhasil dihapus.');
    }
}
