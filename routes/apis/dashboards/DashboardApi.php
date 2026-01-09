<?php

use App\Http\Controllers\api\dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
    Route::get('/comparativa-anios', [DashboardController::class, 'comparativaAnios'])->middleware('can:dashboard')->name('dashboard.comparativaAnios');
    Route::get('/ventas-meses', [DashboardController::class, 'ventaMeses'])->middleware('can:dashboard')->name('dashboard.ventaMeses');
    Route::get('/top-five', [DashboardController::class, 'topFive'])->middleware('can:dashboard')->name('dashboard.topFive');
});
