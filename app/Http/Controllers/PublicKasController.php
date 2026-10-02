<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Services\ArrearsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicKasController extends Controller
{
    public function __construct(
        protected ArrearsService $arrearsService
    ) {}

    public function show(Request $request, string $slug, ?int $year = null, ?int $month = null): View
    {
        $group = Group::where('slug', $slug)->first();

        // If group does not exist or is not public, abort with 404
        if (! $group || ! $group->is_public) {
            abort(404, 'Halaman publik kas ini tidak ditemukan atau bersifat privat.');
        }

        // Only closed periods are listed for the formal monthly breakdown
        $closedPeriods = Period::where('group_id', $group->id)
            ->where('status', 'closed')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $selectedPeriod = null;

        if ($year && $month) {
            $selectedPeriod = $closedPeriods->first(function ($p) use ($year, $month) {
                return $p->year === (int) $year && $p->month === (int) $month;
            });

            // If requested period is not closed or not found, 404
            if (! $selectedPeriod) {
                abort(404, 'Periode kas yang diminta belum ditutup atau tidak tersedia.');
            }
        } else {
            // Default to the latest closed period
            $selectedPeriod = $closedPeriods->first();
        }

        if ($selectedPeriod) {
            $selectedPeriod->load([
                'incomes.member',
                'expenses.category',
                'expenses.attachments',
            ]);
        }

        // Realtime summary metrics for the group across all periods (matching admin dashboard)
        $allGroupPeriods = Period::where('group_id', $group->id)
            ->whereIn('status', ['open', 'closed'])
            ->get();
        $allPeriodIds = $allGroupPeriods->pluck('id');

        $totalIncome = (float) Income::whereIn('period_id', $allPeriodIds)->sum('nominal');
        $totalExpense = (float) Expense::whereIn('period_id', $allPeriodIds)->sum('nominal');
        $totalBalance = $totalIncome - $totalExpense;
        $totalMembers = Member::where('group_id', $group->id)->where('is_active', true)->count();

        // Monthly trends for chart (realtime across periods up to 12)
        $chartPeriods = Period::where('group_id', $group->id)
            ->whereIn('status', ['open', 'closed'])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        foreach ($chartPeriods as $period) {
            $chartLabels[] = $period->period_name;
            $chartIncome[] = (float) $period->total_income;
            $chartExpense[] = (float) $period->total_expense;
        }

        // Category breakdown for doughnut chart (realtime across all periods)
        $categoryExpenses = Expense::whereIn('period_id', $allPeriodIds)
            ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('COALESCE(expense_categories.name, "Tanpa Kategori") as name, SUM(expenses.nominal) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->get();

        $categoryLabels = $categoryExpenses->pluck('name')->toArray();
        $categoryData = $categoryExpenses->pluck('total')->map(fn ($v) => (float) $v)->toArray();

        // Arrears list & compliance breakdown for this group
        $arrears = $this->arrearsService->getArrears($group->id);

        $compliancePaidCount = $arrears->where('total_shortage', '<=', 0)->count();
        $complianceLightCount = $arrears->where('total_shortage', '>', 0)->where('unpaid_months_count', '<=', 2)->count();
        $complianceHeavyCount = $arrears->where('total_shortage', '>', 0)->where('unpaid_months_count', '>', 2)->count();

        $complianceLabels = ['Lunas / Tertib', 'Nunggak 1-2 Bulan', 'Nunggak >2 Bulan'];
        $complianceData = [$compliancePaidCount, $complianceLightCount, $complianceHeavyCount];

        return view('public.show', compact(
            'group',
            'closedPeriods',
            'selectedPeriod',
            'year',
            'month',
            'totalIncome',
            'totalExpense',
            'totalBalance',
            'totalMembers',
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
}
