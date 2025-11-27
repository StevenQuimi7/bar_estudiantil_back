<?php

use App\Http\Controllers\api\cursos\CursoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('cursos')->group(function () {
    Route::get('/', [CursoController::class, 'index'])->name('cursos.index');
    Route::post('guardar/', [CursoController::class, 'store'])->name('cursos.store');
    Route::put('actualizar/{id}', [CursoController::class, 'update'])->name('cursos.update');
    Route::delete('eliminar', [CursoController::class, 'destroy'])->name('cursos.destroy');
});
