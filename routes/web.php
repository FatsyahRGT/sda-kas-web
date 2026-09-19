<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PublicKasController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Halaman Publik (tanpa auth)
Route::prefix('publik')->name('public.')->group(function () {
    Route::get('{slug}', [PublicKasController::class, 'show'])->name('group');
    Route::get('{slug}/{year}/{month}', [PublicKasController::class, 'show'])->name('group.period');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Panel (Protected by web session auth + admin role)
Route::middleware(['auth', 'role.admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/arrears-data', [DashboardController::class, 'arrearsData'])->name('dashboard.arrears-data');

    // Manajemen Grup Kas
    Route::resource('groups', GroupController::class);
    Route::post('groups/{group}/toggle-public', [GroupController::class, 'togglePublic'])->name('groups.toggle-public');

    // Manajemen Anggota
    Route::resource('members', MemberController::class);

    // Manajemen Periode
    Route::resource('periods', PeriodController::class);
    Route::patch('periods/{period}/status', [PeriodController::class, 'updateStatus'])->name('periods.update-status');

    // Pemasukan (Setoran Anggota)
    Route::resource('incomes', IncomeController::class);

    // Pengeluaran
    Route::resource('expenses', ExpenseController::class);
    Route::delete('expenses/attachments/{attachment}', [ExpenseController::class, 'destroyAttachment'])->name('expenses.attachments.destroy');

    // Kategori Pengeluaran
    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['create', 'edit', 'show']);

    // Laporan
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{period}/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('reports/{period}/print', [ReportController::class, 'printPdf'])->name('reports.print');

    // Manajemen User (Khusus Superadmin)
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/generate-token', [UserController::class, 'generateToken'])->name('users.generate-token');
        Route::post('users/{user}/revoke-tokens', [UserController::class, 'revokeTokens'])->name('users.revoke-tokens');
    });
});
