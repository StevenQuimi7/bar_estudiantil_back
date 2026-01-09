<?php

use App\Http\Controllers\api\documentos\DocumentoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('documentos')->group(function () {
    Route::post('/guardarTemporal', [DocumentoController::class, 'guardarTemporal'])->middleware('can:documento')->name('documentos.guardarTemporal');
});
