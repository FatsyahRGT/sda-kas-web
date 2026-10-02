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
            $chartIncome[] = (float) $period->total_income;
            $chartExpense[] = (float) $period->total_expense;
        }

        // Category breakdown for doughnut chart
        $categoryExpenses = Expense::whereIn('period_id', $periodIds)
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('COALESCE(expense_categories.name, "Tanpa Kategori") as name, SUM(expenses.nominal) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $categoryLabels = $categoryExpenses->pluck('name')->toArray();
        $categoryData = $categoryExpenses->pluck('total')->map(fn ($v) => (float) $v)->toArray();

        // Initial Arrears List & Compliance breakdown
        $arrears = $arrearsService->getArrears($selectedGroupId);

        $compliancePaidCount = $arrears->where('total_shortage', '<=', 0)->count();
        $complianceLightCount = $arrears->where('total_shortage', '>', 0)->where('unpaid_months_count', '<=', 2)->count();
        $complianceHeavyCount = $arrears->where('total_shortage', '>', 0)->where('unpaid_months_count', '>', 2)->count();

        $complianceLabels = ['Lunas / Tertib', 'Nunggak 1-2 Bulan', 'Nunggak >2 Bulan'];
        $complianceData = [$compliancePaidCount, $complianceLightCount, $complianceHeavyCount];

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
            'categoryLabels',
            'categoryData',
            'complianceLabels',
            'complianceData',
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
