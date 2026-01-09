<?php

namespace App\Observers\venta;

use App\Models\venta\DetalleVenta;

class DetalleVentaObserver
{

    //
    public function created(DetalleVenta $detalle): void
    {
        //
        $detalle->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($detalle),
            "id_usuario_creacion" => $detalle->id_usuario_creacion
        ]);
    }

    /**
     * Handle the DetalleVenta "updated" event.
     */
    public function updated(DetalleVenta $detalle): void
    {
        //
        if ($detalle->wasChanged('activo') && $detalle->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $detalle->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($detalle->getOriginal()),
            "data_actual"   => json_encode($detalle->getAttributes()),
            "id_usuario_creacion" => $detalle->id_usuario_creacion
        ]);
    }
}
