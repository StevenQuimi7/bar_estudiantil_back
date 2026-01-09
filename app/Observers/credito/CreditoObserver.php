<?php

namespace App\Observers\credito;

use App\Models\cliente\credito\Credito;

class CreditoObserver
{
    //
    public function created(Credito $credito): void
    {
        //
        $credito->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($credito),
            "id_usuario_creacion" => $credito->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Credito "updated" event.
     */
    public function updated(Credito $credito): void
    {
        //
        if ($credito->wasChanged('activo') && $credito->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $credito->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($credito->getOriginal()),
            "data_actual"   => json_encode($credito->getAttributes()),
            "id_usuario_creacion" => $credito->id_usuario_creacion
        ]);
    }
}
