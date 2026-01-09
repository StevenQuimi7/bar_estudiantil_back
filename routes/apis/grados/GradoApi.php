<?php

use App\Http\Controllers\api\grados\GradoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('grados')->group(function () {
    Route::get('/', [GradoController::class, 'index'])->middleware('can:grado')->name('grados.index');
    Route::get('/comboGrados', [GradoController::class, 'comboGrados'])->name('grados.comboGrados');
    Route::post('guardar/', [GradoController::class, 'store'])->middleware('can:grado.store')->name('grados.store');
    Route::put('actualizar/{id}', [GradoController::class, 'update'])->middleware('can:grado.update')->name('grados.update');
    Route::delete('eliminar', [GradoController::class, 'destroy'])->middleware('can:grado.delete')->name('grados.destroy');
});
