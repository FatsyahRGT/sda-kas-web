<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ExpenseCategory::withCount('expenses')->orderBy('name')->get();

        return view('expense_categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_categories,name'],
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:expense_categories,name,'.$expenseCategory->id],
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Kategori pengeluaran berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}
