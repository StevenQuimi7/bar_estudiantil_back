<?php

use App\Http\Controllers\api\cursos\CursoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('cursos')->group(function () {
    Route::get('/', [CursoController::class, 'index'])->middleware('can:curso')->name('cursos.index');
    Route::get('/comboCursos', [CursoController::class, 'comboCursos'])->name('cursos.comboCursos');
    Route::post('guardar/', [CursoController::class, 'store'])->middleware('can:curso.store')->name('cursos.store');
    Route::put('actualizar/{id}', [CursoController::class, 'update'])->middleware('can:curso.update')->name('cursos.update');
    Route::delete('eliminar', [CursoController::class, 'destroy'])->middleware('can:curso.delete')->name('cursos.destroy');
});
