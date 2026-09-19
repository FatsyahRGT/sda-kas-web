<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Services\ArrearsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutomationController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;

        $periodsQuery = Period::when($groupId, fn ($q) => $q->where('group_id', $groupId));
        $periodIds = $periodsQuery->pluck('id');

        $totalIncome = (float) Income::whereIn('period_id', $periodIds)->sum('nominal');
        $totalExpense = (float) Expense::whereIn('period_id', $periodIds)->sum('nominal');

        return response()->json([
            'success' => true,
            'data' => [
                'group_id' => $groupId,
                'total_groups' => Group::count(),
                'total_members' => Member::when($groupId, fn ($q) => $q->where('group_id', $groupId))->count(),
                'total_periods' => $periodsQuery->count(),
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'total_balance' => $totalIncome - $totalExpense,
            ],
        ]);
    }

    public function groups(): JsonResponse
    {
        $groups = Group::withCount(['members', 'periods'])->get();

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    public function members(Request $request): JsonResponse
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;

        $members = Member::when($groupId, fn ($q) => $q->where('group_id', $groupId))
            ->with('group:id,name,slug')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

    public function periods(Request $request): JsonResponse
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;

        $periods = Period::when($groupId, fn ($q) => $q->where('group_id', $groupId))
            ->with('group:id,name')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'group_id' => $p->group_id,
                    'group_name' => $p->group?->name,
                    'period_name' => $p->period_name,
                    'month' => $p->month,
                    'year' => $p->year,
                    'due_amount' => $p->due_amount,
                    'effective_due_amount' => $p->effective_due_amount,
                    'status' => $p->status,
                    'total_income' => $p->total_income,
                    'total_expense' => $p->total_expense,
                    'balance' => $p->balance,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $periods,
        ]);
    }

    public function storeIncome(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id'],
            'member_id' => ['required', 'exists:members,id'],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $income = Income::create([
            'period_id' => $validated['period_id'],
            'member_id' => $validated['member_id'],
            'nominal' => $validated['nominal'],
            'transaction_date' => $validated['transaction_date'],
            'note' => $validated['note'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income recorded successfully.',
            'data' => $income,
        ], Response::HTTP_CREATED);
    }

    public function storeExpense(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id'],
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
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

        return response()->json([
            'success' => true,
            'message' => 'Expense recorded successfully.',
            'data' => $expense,
        ], Response::HTTP_CREATED);
    }

    public function arrears(Request $request, ArrearsService $arrearsService): JsonResponse
    {
        $groupId = $request->filled('group_id') ? (int) $request->group_id : null;
        $periodId = $request->filled('period_id') ? (int) $request->period_id : null;

        $results = $arrearsService->getArrears($groupId, $periodId);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}
