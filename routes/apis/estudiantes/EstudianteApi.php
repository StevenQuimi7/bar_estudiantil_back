<?php

use App\Http\Controllers\api\estudiantes\EstudianteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('estudiantes')->group(function () {
    Route::get('/', [EstudianteController::class, 'index'])->middleware('can:estudiante')->name('estudiantes.index');
    Route::post('guardar/', [EstudianteController::class, 'store'])->middleware('can:estudiante.store')->name('estudiantes.store');
    Route::put('actualizar/{id}', [EstudianteController::class, 'update'])->middleware('can:estudiante.update')->name('estudiantes.update');
    Route::delete('eliminar/{id}', [EstudianteController::class, 'destroy'])->middleware('can:estudiante.delete')->name('estudiantes.destroy');
});
