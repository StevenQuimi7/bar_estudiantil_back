<?php

use App\Http\Controllers\api\especialidades\EspecialidadController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('especialidades')->group(function () {
    Route::get('/', [EspecialidadController::class, 'index'])->name('especialidades.index');
    Route::get('/comboEspecialidades', [EspecialidadController::class, 'comboEspecialidades'])->name('especialidades.comboEspecialidades');
    Route::post('guardar/', [EspecialidadController::class, 'store'])->name('especialidades.store');
    Route::put('actualizar/{id}', [EspecialidadController::class, 'update'])->name('especialidades.update');
    Route::delete('eliminar', [EspecialidadController::class, 'destroy'])->name('especialidades.destroy');
});
