<?php

namespace App\Providers;

use App\Models\categoria\Categoria;
use App\Models\cliente\Cliente;
use App\Models\curso\Curso;
use App\Models\especialidad\Especialidad;
use App\Models\grado\Grado;
use App\Models\nivel\Nivel;
use App\Models\producto\Producto;
use App\Models\venta\Venta;
use App\Observers\categoria\CategoriaObserver;
use App\Observers\cliente\ClienteObserver;
use App\Observers\curso\CursoObserver;
use App\Observers\especialidad\EspecialidadObserver;
use App\Observers\grado\GradoObserver;
use App\Observers\nivel\NivelObserver;
use App\Observers\producto\ProductoObserver;
use App\Observers\venta\VentaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Especialidad::observe(EspecialidadObserver::class);
        Grado::observe(GradoObserver::class);
        Nivel::observe(NivelObserver::class);
        Categoria::observe(CategoriaObserver::class);
        Cliente::observe(ClienteObserver::class);
        Curso::observe(CursoObserver::class);
        Venta::observe(VentaObserver::class);
        Producto::observe(ProductoObserver::class);
    }
}
