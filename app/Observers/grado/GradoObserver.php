<?php

namespace App\Observers\grado;

use App\Models\grado\Grado;

class GradoObserver
{
    /**
     * Handle the Grado "created" event.
     */
    public function created(Grado $grado): void
    {
        //
        $grado->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($grado),
            "id_usuario_creacion" => $grado->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Grado "updated" event.
     */
    public function updated(Grado $grado): void
    {
        //
        $grado->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($grado->getPrevious()),
            "data_actual"=>json_encode($grado),
            "id_usuario_creacion" => $grado->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Grado "deleted" event.
     */
    public function deleted(Grado $grado): void
    {
        //
    }

    /**
     * Handle the Grado "restored" event.
     */
    public function restored(Grado $grado): void
    {
        //
    }

    /**
     * Handle the Grado "force deleted" event.
     */
    public function forceDeleted(Grado $grado): void
    {
        //
    }
}
