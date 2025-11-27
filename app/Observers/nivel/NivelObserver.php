<?php

namespace App\Observers\nivel;

use App\Models\nivel\Nivel;

class NivelObserver
{
    /**
     * Handle the Nivel "created" event.
     */
    public function created(Nivel $nivel): void
    {
        //
        $nivel->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($nivel),
            "id_usuario_creacion" => $nivel->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Nivel "updated" event.
     */
    public function updated(Nivel $nivel): void
    {
        //
        $nivel->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($nivel->getPrevious()),
            "data_actual"=>json_encode($nivel),
            "id_usuario_creacion" => $nivel->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Nivel "deleted" event.
     */
    public function deleted(Nivel $nivel): void
    {
        //
    }

    /**
     * Handle the Nivel "restored" event.
     */
    public function restored(Nivel $nivel): void
    {
        //
    }

    /**
     * Handle the Nivel "force deleted" event.
     */
    public function forceDeleted(Nivel $nivel): void
    {
        //
    }
}
