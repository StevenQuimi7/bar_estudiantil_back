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
        $producto->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($producto->getPrevious()),
            "data_actual"=>json_encode($producto),
            "id_usuario_creacion" => $producto->id_usuario_creacion
        ]);
    }
}
