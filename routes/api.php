<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\InventoryController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/transactions', [TransactionController::class,'store']);
    Route::post('/transactions/{transaction}/void', [TransactionController::class,'void']);
    Route::get('/dashboard/summary', [DashboardController::class,'summary']);
    Route::get('/dashboard/chart', [DashboardController::class,'chart']);
    Route::get('/inventory/low-stock', [InventoryController::class,'lowStock']);
});
