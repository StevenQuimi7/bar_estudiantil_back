<?php

use App\Http\Controllers\api\creditos\CreditoController;
use Illuminate\Support\Facades\Route;

Route::prefix('creditos')->group(function () {
    Route::get('/find/{id}', [CreditoController::class, 'index'])->name('creditos.find');
    Route::get('/movimientos/{id_credito}', [CreditoController::class, 'movimientos'])->name('creditos.movimientos');
    Route::post('/store', [CreditoController::class, 'store'])->name('creditos.store');
})->middleware('auth:sanctum');
