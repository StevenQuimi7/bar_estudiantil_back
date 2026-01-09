<?php

use App\Http\Controllers\api\niveles\NivelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('niveles')->group(function () {
    Route::get('/', [NivelController::class, 'index'])->middleware('can:nivel')->name('niveles.index');
    Route::get('/comboNiveles', [NivelController::class, 'comboNiveles'])->name('niveles.comboNiveles');
    Route::post('guardar/', [NivelController::class, 'store'])->middleware('can:nivel.store')->name('niveles.store');
    Route::put('actualizar/{id}', [NivelController::class, 'update'])->middleware('can:nivel.update')->name('niveles.update');
    Route::delete('eliminar', [NivelController::class, 'destroy'])->middleware('can:nivel.delete')->name('niveles.destroy');
});
