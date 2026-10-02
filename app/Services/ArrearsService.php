<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Income;
use App\Models\Period;
use Illuminate\Support\Collection;

class ArrearsService
{
    /**
     * Calculate arrears data for active members in a group.
     *
     * @param  int|null  $periodId  Filter up to or specifically for a period
     */
    public function getArrears(?int $groupId = null, ?int $periodId = null): Collection
    {
        $groupQuery = Group::query();
        if ($groupId) {
            $groupQuery->where('id', $groupId);
        }
        $groups = $groupQuery->with(['members' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        $results = collect();

        foreach ($groups as $group) {
            $periodQuery = Period::where('group_id', $group->id)
                ->whereIn('status', ['open', 'closed'])
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc');

            if ($periodId) {
                // If specific period is requested, check only that period
                $periodQuery->where('id', $periodId);
            }

            $periods = $periodQuery->get();

            if ($periods->isEmpty()) {
                continue;
            }

            // Preload incomes for these periods
            $periodIds = $periods->pluck('id');
            $activeMembers = $group->members;

            // Load incomes indexed by member_id and period_id
            $incomes = Income::whereIn('period_id', $periodIds)
                ->whereIn('member_id', $activeMembers->pluck('id'))
                ->get()
                ->groupBy('member_id');

            foreach ($activeMembers as $member) {
                $memberIncomes = $incomes->get($member->id, collect());

                $totalExpected = 0.0;
                $totalPaid = 0.0;
                $unpaidPeriods = [];
                $allPeriods = [];

                foreach ($periods as $period) {
                    $due = (float) $period->effective_due_amount;
                    $paidForPeriod = (float) $memberIncomes
                        ->where('period_id', $period->id)
                        ->sum('nominal');

                    $totalExpected += $due;
                    $totalPaid += $paidForPeriod;

                    $shortage = max(0.0, $due - $paidForPeriod);
                    $isPaid = $paidForPeriod >= $due && $due > 0;
                    $isPartial = $paidForPeriod > 0 && $paidForPeriod < $due;
                    $isUnpaid = $paidForPeriod <= 0 && $due > 0;

                    $periodSummary = [
                        'period_id' => $period->id,
                        'period_name' => $period->period_name,
                        'year' => $period->year,
                        'month' => $period->month,
                        'status' => $period->status,
                        'due_amount' => $due,
                        'paid_amount' => $paidForPeriod,
                        'shortage' => $shortage,
                        'is_paid' => $isPaid,
                        'is_partial' => $isPartial,
                        'is_unpaid' => $isUnpaid,
                    ];

                    $allPeriods[] = $periodSummary;

                    if ($paidForPeriod < $due) {
                        $unpaidPeriods[] = $periodSummary;
                    }
                }

                $totalShortage = max(0.0, $totalExpected - $totalPaid);
                $unpaidMonthsCount = count($unpaidPeriods);
                $paymentPercentage = $totalExpected > 0 ? round(min(100.0, ($totalPaid / $totalExpected) * 100), 1) : 100.0;

                $results->push([
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'member_phone' => $member->phone,
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'total_expected' => $totalExpected,
                    'total_paid' => $totalPaid,
                    'total_shortage' => $totalShortage,
                    'payment_percentage' => $paymentPercentage,
                    'unpaid_months_count' => $unpaidMonthsCount,
                    'has_arrears' => $unpaidMonthsCount > 0 && $totalShortage > 0,
                    'unpaid_periods' => $unpaidPeriods,
                    'all_periods' => $allPeriods,
                ]);
            }
        }

        // Sort: highest arrears on top by default
        return $results->sortByDesc('total_shortage')->values();
    }
}
