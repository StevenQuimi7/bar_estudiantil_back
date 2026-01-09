<?php

use App\Http\Controllers\api\productos\ProductoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index'])->middleware('can:producto')->name('productos.index');
    Route::get('/descargarPlantilla', [ProductoController::class, 'descargarPlantilla'])->middleware('can:producto.descargarPlantilla')->name('productos.descargarPlantilla');
    Route::post('/cargaMasiva', [ProductoController::class, 'cargaMasiva'])->middleware('can:producto.cargaMasiva')->name('productos.cargaMasiva');
    Route::post('/exportar', [ProductoController::class, 'exportar'])->middleware('can:producto.exportar')->name('productos.exportar');
    Route::post('guardar/', [ProductoController::class, 'store'])->middleware('can:producto.store')->name('productos.store');
    Route::put('actualizar/{id}', [ProductoController::class, 'update'])->middleware('can:producto.update')->name('productos.update');
    Route::delete('eliminar/{id}', [ProductoController::class, 'destroy'])->middleware('can:producto.destroy')->name('productos.destroy');
});
