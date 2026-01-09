<?php

use App\Http\Controllers\api\roles\RolController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('roles')->group(function () {
    Route::get('/', [RolController::class, 'index'])->middleware('can:role')->name('roles.index');
    Route::get('/listadoPermisos', [RolController::class, 'listadoPermisos'])->name('roles.listadoPermisos');
    Route::get('/listadoRoles', [RolController::class, 'comboRoles'])->name('roles.comboRoles');
    Route::post('guardar/', [RolController::class, 'store'])->middleware('can:role.store')->name('roles.store');
    Route::put('actualizar/{id}', [RolController::class, 'update'])->middleware('can:role.update')->name('roles.update');
    Route::delete('eliminar', [RolController::class, 'destroy'])->middleware('can:role.destroy')->name('roles.destroy');
});
