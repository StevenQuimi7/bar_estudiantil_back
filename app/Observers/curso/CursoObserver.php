<?php

namespace App\Observers\curso;

use App\Models\curso\Curso;

class CursoObserver
{
    //
    public function created(Curso $curso): void
    {
        //
        $curso->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($curso),
            "id_usuario_creacion" => $curso->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Curso "updated" event.
     */
    public function updated(Curso $curso): void
    {
        //
        $curso->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($curso->getPrevious()),
            "data_actual"=>json_encode($curso),
            "id_usuario_creacion" => $curso->id_usuario_creacion
        ]);
    }
}
