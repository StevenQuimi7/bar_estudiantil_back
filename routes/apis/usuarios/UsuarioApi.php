<?php

use App\Http\Controllers\api\usuarios\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->middleware('can:usuario')->name('usuarios.index');
    Route::get('/perfil', [UsuarioController::class, 'perfilUsuario'])->name('usuarios.perfilUsuario');
    Route::post('guardar/', [UsuarioController::class, 'store'])->middleware('can:usuario.store')->name('usuarios.store');
    Route::put('actualizar/{id}', [UsuarioController::class, 'update'])->middleware('can:usuario.update')->name('usuarios.update');
    Route::delete('eliminar', [UsuarioController::class, 'destroy'])->middleware('can:usuario.destroy')->name('usuarios.destroy');
    Route::put('/activarUsuario/{id}', [UsuarioController::class, 'activarUsuario'])->name('usuarios.activarUsuario');
});
