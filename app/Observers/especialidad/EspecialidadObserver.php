<?php

namespace App\Observers\especialidad;

use App\Models\especialidad\Especialidad;

class EspecialidadObserver
{
    /**
     * Handle the Especialidad "created" event.
     */
    public function created(Especialidad $especialidad): void
    {
        //
        $especialidad->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($especialidad),
            "id_usuario_creacion" => $especialidad->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Especialidad "updated" event.
     */
    public function updated(Especialidad $especialidad): void
    {
        //
        $especialidad->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($especialidad->getPrevious()),
            "data_actual"=>json_encode($especialidad),
            "id_usuario_creacion" => $especialidad->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Especialidad "deleted" event.
     */
    public function deleted(Especialidad $especialidad): void
    {
        //
    }

    /**
     * Handle the Especialidad "restored" event.
     */
    public function restored(Especialidad $especialidad): void
    {
        //
    }

    /**
     * Handle the Especialidad "force deleted" event.
     */
    public function forceDeleted(Especialidad $especialidad): void
    {
        //
    }
}
