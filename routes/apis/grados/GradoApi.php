<?php

use App\Http\Controllers\api\grados\GradoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('grados')->group(function () {
    Route::get('/', [GradoController::class, 'index'])->name('grados.index');
    Route::get('/comboGrados', [GradoController::class, 'comboGrados'])->name('grados.comboGrados');
    Route::post('guardar/', [GradoController::class, 'store'])->name('grados.store');
    Route::put('actualizar/{id}', [GradoController::class, 'update'])->name('grados.update');
    Route::delete('eliminar', [GradoController::class, 'destroy'])->name('grados.destroy');
});
