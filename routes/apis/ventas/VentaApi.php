<?php

use App\Http\Controllers\api\ventas\VentaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('ventas')->group(function () {
    Route::get('/', [VentaController::class, 'index'])->middleware('can:venta')->name('ventas.index');
    Route::post('guardar/', [VentaController::class, 'store'])->middleware('can:venta.store')->name('ventas.store');
    Route::post('/exportar', [VentaController::class, 'exportar'])->middleware('can:venta.exportar')->name('productos.exportar');
    Route::put('updateEstadoGestion/{id}', [VentaController::class, 'updateEstadoGestion'])->middleware('can:venta.updateEstadoGestion')->name('ventas.updateEstadoGestion');
    Route::delete('eliminar/{id}', [VentaController::class, 'destroy'])->middleware('can:venta.destroy')->name('ventas.destroy');
});
