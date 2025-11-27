<?php

use App\Http\Controllers\api\productos\ProductoController;
use Illuminate\Support\Facades\Route;

Route::prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/descargarPlantilla', [ProductoController::class, 'descargarPlantilla'])->name('productos.descargarPlantilla');
    Route::post('/cargaMasiva', [ProductoController::class, 'cargaMasiva'])->name('productos.cargaMasiva');
    Route::post('guardar/', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('actualizar/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('eliminar/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
})->middleware('auth:sanctum');
