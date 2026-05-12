<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\OwnerReportController;
use App\Http\Controllers\Api\TransactionController;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::post('/api/transactions', [TransactionController::class, 'store'])
        ->name('api.transactions.store');

    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', fn () => redirect()->route('kasir.pos'));
        Route::get('/open-shift',  [KasirController::class, 'openShift'])->name('open-shift');
        Route::post('/start-shift',[KasirController::class, 'startShift'])->name('start-shift');
        Route::post('/close-shift',[KasirController::class, 'closeShift'])->name('close-shift');

        Route::post('/expenses/store', [KasirController::class, 'storeExpense'])->name('expenses.store');

        Route::post('/products',             [KasirController::class, 'storeMainProduct'])->name('products.store');
        Route::delete('/products/{product}', [KasirController::class, 'deleteMainProduct'])->name('products.delete');

        Route::post('/add-consignment',                  [KasirController::class, 'addConsignment'])->name('add-consignment');
        Route::post('/consignment-products',             [KasirController::class, 'storeConsignmentProduct'])->name('consignment-products.store');
        Route::delete('/consignment-products/{product}', [KasirController::class, 'deleteConsignmentProduct'])->name('consignment-products.delete');

        Route::get('/pos',        [KasirController::class, 'index'])->name('pos');
        Route::get('/csrf-token', [KasirController::class, 'csrfToken'])->name('csrf-token');
    });

    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/', fn () => redirect()->route('owner.dashboard'));

        Route::get('/dashboard',        [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/live',   [OwnerController::class, 'dashboardLive'])->name('dashboard.live');
        Route::get('/dashboard/stream', [OwnerController::class, 'dashboardStream'])->name('dashboard.stream');

        Route::get('/income/daily',   [OwnerController::class, 'dailyIncome'])->name('income.daily');
        Route::get('/income/monthly', [OwnerController::class, 'monthlyIncome'])->name('income.monthly');

        Route::get('/reports/transactions/pdf', [OwnerReportController::class, 'transactionsPdf'])
            ->name('reports.transactions.pdf');
        Route::get('/reports/transactions/excel', [OwnerReportController::class, 'transactionsExcel'])
            ->name('reports.transactions.excel');
        Route::get('/reports/expenses/pdf', [OwnerReportController::class, 'expensesPdf'])
            ->name('reports.expenses.pdf');
        Route::get('/reports/expenses/excel', [OwnerReportController::class, 'expensesExcel'])
            ->name('reports.expenses.excel');

        Route::post('/expenses/store',       [OwnerController::class, 'storeExpense'])->name('expenses.store');
        Route::delete('/expenses/{expense}', [OwnerController::class, 'deleteExpense'])->name('expenses.delete');
        Route::get('/expenses/daily',        [OwnerController::class, 'dailyExpense'])->name('expenses.daily');
        Route::get('/expenses/monthly',      [OwnerController::class, 'monthlyExpense'])->name('expenses.monthly');
    });
});
