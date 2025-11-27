<?php

use App\Http\Controllers\api\clientes\ClienteController;
use Illuminate\Support\Facades\Route;

Route::prefix('clientes')->group(function () {
    Route::get('/', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/descargarPlantilla', [ClienteController::class, 'descargarPlantilla'])->name('clientes.descargarPlantilla');
    Route::post('/cargaMasiva', [ClienteController::class, 'cargaMasiva'])->name('clientes.cargaMasiva');
    Route::post('guardar/', [ClienteController::class, 'store'])->name('clientes.store');
    Route::put('actualizar/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('eliminar/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
})->middleware('auth:sanctum');
