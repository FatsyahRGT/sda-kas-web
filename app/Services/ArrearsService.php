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

                foreach ($periods as $period) {
                    $due = (float) $period->effective_due_amount;
                    $paidForPeriod = (float) $memberIncomes
                        ->where('period_id', $period->id)
                        ->sum('nominal');

                    $totalExpected += $due;
                    $totalPaid += $paidForPeriod;

                    if ($paidForPeriod < $due) {
                        $shortage = $due - $paidForPeriod;
                        $unpaidPeriods[] = [
                            'period_id' => $period->id,
                            'period_name' => $period->period_name,
                            'year' => $period->year,
                            'month' => $period->month,
                            'status' => $period->status,
                            'due_amount' => $due,
                            'paid_amount' => $paidForPeriod,
                            'shortage' => $shortage,
                        ];
                    }
                }

                $totalShortage = max(0.0, $totalExpected - $totalPaid);
                $unpaidMonthsCount = count($unpaidPeriods);

                $results->push([
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'member_phone' => $member->phone,
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'total_expected' => $totalExpected,
                    'total_paid' => $totalPaid,
                    'total_shortage' => $totalShortage,
                    'unpaid_months_count' => $unpaidMonthsCount,
                    'has_arrears' => $unpaidMonthsCount > 0 && $totalShortage > 0,
                    'unpaid_periods' => $unpaidPeriods,
                ]);
            }
        }

        // Sort: highest arrears on top by default
        return $results->sortByDesc('total_shortage')->values();
    }
}
