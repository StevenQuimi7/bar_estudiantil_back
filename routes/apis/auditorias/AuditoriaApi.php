<?php

use App\Http\Controllers\api\auditorias\AuditoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('auditorias')->group(function () {
    Route::get('/', [AuditoriaController::class, 'index'])->middleware('can:auditoria')->name('auditorias.index');
    Route::get('/comboModulos', [AuditoriaController::class, 'comboModulos'])->name('auditorias.comboModulos');
    Route::get('/comboAcciones', [AuditoriaController::class, 'comboAcciones'])->name('auditorias.comboAcciones');
});
