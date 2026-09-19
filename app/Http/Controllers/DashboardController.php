<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Services\ArrearsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ArrearsService $arrearsService): View
    {
        $selectedGroupId = $request->has('group_id') && $request->group_id !== ''
            ? (int) $request->group_id
            : null;

        $groups = Group::orderBy('name')->get();

        // Selected group or default to first group
        $currentGroup = null;
        if ($selectedGroupId) {
            $currentGroup = $groups->firstWhere('id', $selectedGroupId);
        } elseif ($groups->isNotEmpty()) {
            $currentGroup = $groups->first();
            $selectedGroupId = $currentGroup->id;
        }

        // Summary metrics
        $groupScope = $selectedGroupId ? fn ($q) => $q->where('group_id', $selectedGroupId) : null;

        $totalMembers = Member::when($selectedGroupId, fn ($q) => $q->where('group_id', $selectedGroupId))
            ->where('is_active', true)
            ->count();

        $periodsQuery = Period::when($selectedGroupId, fn ($q) => $q->where('group_id', $selectedGroupId));
        $periodIds = $periodsQuery->pluck('id');

        $totalIncome = (float) Income::whereIn('period_id', $periodIds)->sum('nominal');
        $totalExpense = (float) Expense::whereIn('period_id', $periodIds)->sum('nominal');
        $totalBalance = $totalIncome - $totalExpense;

        // Monthly trends for chart
        $periods = Period::when($selectedGroupId, fn ($q) => $q->where('group_id', $selectedGroupId))
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        foreach ($periods as $period) {
            $chartLabels[] = $period->period_name;
            $chartIncome[] = $period->total_income;
            $chartExpense[] = $period->total_expense;
        }

        // Initial Arrears List
        $arrears = $arrearsService->getArrears($selectedGroupId);

        return view('dashboard.index', compact(
            'groups',
            'currentGroup',
            'selectedGroupId',
            'totalMembers',
            'totalIncome',
            'totalExpense',
            'totalBalance',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'arrears'
        ));
    }

    public function arrearsData(Request $request, ArrearsService $arrearsService): JsonResponse
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $periodId = $request->filled('period_id') ? (int) $request->period_id : null;
        $onlyArrears = $request->boolean('only_arrears', false);

        $results = $arrearsService->getArrears($groupId, $periodId);

        if ($onlyArrears) {
            $results = $results->where('total_shortage', '>', 0)->values();
        }

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}
