<?php

use App\Http\Controllers\api\auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
require __DIR__.'/apis/categorias/CategoriaApi.php';
require __DIR__.'/apis/especialidades/EspecialidadApi.php';
require __DIR__.'/apis/grados/GradoApi.php';
require __DIR__.'/apis/cursos/CursoApi.php';
require __DIR__.'/apis/niveles/NivelApi.php';
require __DIR__.'/apis/roles/RolApi.php';
require __DIR__.'/apis/usuarios/UsuarioApi.php';
require __DIR__.'/apis/productos/ProductoApi.php';
require __DIR__.'/apis/clientes/ClienteApi.php';
require __DIR__.'/apis/clientes/CreditoApi.php';

Route::post('/login',[AuthController::class, 'login'] );
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

