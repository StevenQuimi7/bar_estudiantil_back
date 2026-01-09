<?php

namespace App\Observers\venta;

use App\Models\venta\Venta;

class VentaObserver
{

    //
    public function created(Venta $venta): void
    {
        //
        $venta->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($venta),
            "id_usuario_creacion" => $venta->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Venta "updated" event.
     */
    public function updated(Venta $venta): void
    {
        //
        if ($venta->wasChanged('activo') && $venta->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $venta->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($venta->getOriginal()),
            "data_actual"   => json_encode($venta->getAttributes()),
            "id_usuario_creacion" => $venta->id_usuario_creacion
        ]);
    }
}
