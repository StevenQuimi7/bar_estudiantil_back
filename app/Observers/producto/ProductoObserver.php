<?php

namespace App\Observers\producto;

use App\Models\producto\Producto;

class ProductoObserver
{
    //
    public function created(Producto $producto): void
    {
        //
        $producto->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($producto),
            "id_usuario_creacion" => $producto->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Producto "updated" event.
     */
    public function updated(Producto $producto): void
    {
        //
        if ($producto->wasChanged('activo') && $producto->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }
        $producto->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($producto->getOriginal()),
            "data_actual"   => json_encode($producto->getAttributes()),
            "id_usuario_creacion" => $producto->id_usuario_creacion
        ]);
    }
}
