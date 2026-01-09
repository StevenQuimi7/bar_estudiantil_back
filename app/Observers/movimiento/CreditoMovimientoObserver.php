<?php

namespace App\Observers\movimiento;

use App\Models\cliente\credito\CreditoMovimiento;

class CreditoMovimientoObserver
{
    //
    public function created(CreditoMovimiento $movimiento): void
    {
        //
        $movimiento->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($movimiento),
            "id_usuario_creacion" => $movimiento->id_usuario_creacion
        ]);
    }

    /**
     * Handle the CreditoMovimiento "updated" event.
     */
    public function updated(CreditoMovimiento $movimiento): void
    {
        //
        if ($movimiento->wasChanged('activo') && $movimiento->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $movimiento->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($movimiento->getOriginal()),
            "data_actual"   => json_encode($movimiento->getAttributes()),
            "id_usuario_creacion" => $movimiento->id_usuario_creacion
        ]);
    }
}
