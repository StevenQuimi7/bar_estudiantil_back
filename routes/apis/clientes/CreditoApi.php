<?php

use App\Http\Controllers\api\clientes\CreditoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('creditos')->group(function () {
    Route::get('/creditoCliente', [CreditoController::class, 'creditoCliente'])->middleware('can:credito.creditoCliente')->name('creditos.creditoCliente');
    Route::get('/movimientos', [CreditoController::class, 'movimientos'])->middleware('can:credito.movimientos')->name('creditos.movimientos');
    Route::post('/guardar', [CreditoController::class, 'store'])->middleware('can:credito.store')->name('creditos.store');
});
