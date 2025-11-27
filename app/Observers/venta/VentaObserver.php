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
        $venta->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($venta->getPrevious()),
            "data_actual"=>json_encode($venta),
            "id_usuario_creacion" => $venta->id_usuario_creacion
        ]);
    }
}
