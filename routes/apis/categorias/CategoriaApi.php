<?php

use App\Http\Controllers\api\categorias\CategoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('categorias')->group(function () {
    Route::get('/comboCategorias', [CategoriaController::class, 'comboCategorias'])->name('categorias.comboCategorias');
    Route::get('/', [CategoriaController::class, 'index'])->middleware('can:categoria')->name('categorias.index');
    Route::post('guardar/', [CategoriaController::class, 'store'])->middleware('can:categoria.store')->name('categorias.store');
    Route::put('actualizar/{id}', [CategoriaController::class, 'update'])->middleware('can:categoria.update')->name('categorias.update');
    Route::delete('eliminar', [CategoriaController::class, 'destroy'])->middleware('can:categoria.delete')->name('categorias.destroy');
});
