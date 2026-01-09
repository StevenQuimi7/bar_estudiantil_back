<?php

use App\Http\Controllers\api\clientes\ClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('clientes')->group(function () {
    Route::get('/', [ClienteController::class, 'index'])->middleware('can:cliente')->name('clientes.index');
    Route::get('/tiposClientes', [ClienteController::class, 'comboTipoCliente'])->name('clientes.comboTipoCliente');
    Route::get('/comboClientes', [ClienteController::class, 'comboClientes'])->name('clientes.comboClientes');
    Route::get('/descargarPlantilla', [ClienteController::class, 'descargarPlantilla'])->middleware('can:cliente.descargarPlantilla')->name('clientes.descargarPlantilla');
    Route::post('/cargaMasiva', [ClienteController::class, 'cargaMasiva'])->middleware('can:cliente.cargaMasiva')->name('clientes.cargaMasiva');
    Route::post('/exportar', [ClienteController::class, 'exportar'])->middleware('can:cliente.exportar')->name('clientes.exportar');
    Route::post('/guardar', [ClienteController::class, 'store'])->middleware('can:cliente.store')->name('clientes.store');
    Route::put('actualizar/{id}', [ClienteController::class, 'update'])->middleware('can:cliente.update')->name('clientes.update');
    Route::delete('eliminar/{id}', [ClienteController::class, 'destroy'])->middleware('can:cliente.delete')->name('clientes.destroy');
});
