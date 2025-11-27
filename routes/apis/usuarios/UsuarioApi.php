<?php

use App\Http\Controllers\api\usuarios\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('guardar/', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('actualizar/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('eliminar', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    Route::put('/activarUsuario/{id}', [UsuarioController::class, 'activarUsuario'])->name('usuarios.activarUsuario');
});
