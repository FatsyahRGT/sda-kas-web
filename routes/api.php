<?php

use App\Http\Controllers\Api\AutomationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Automation API endpoints - protected strictly by Sanctum Bearer Token and Superadmin role
Route::prefix('v1')->middleware(['auth:sanctum', 'role.superadmin.api'])->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    });

    Route::get('/summary', [AutomationController::class, 'summary']);
    Route::get('/groups', [AutomationController::class, 'groups']);
    Route::get('/members', [AutomationController::class, 'members']);
    Route::get('/periods', [AutomationController::class, 'periods']);
    Route::post('/incomes', [AutomationController::class, 'storeIncome']);
    Route::post('/expenses', [AutomationController::class, 'storeExpense']);
    Route::get('/arrears', [AutomationController::class, 'arrears']);
});
