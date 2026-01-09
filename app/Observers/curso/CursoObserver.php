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
        if ($curso->wasChanged('activo') && $curso->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $curso->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($curso->getOriginal()),
            "data_actual"   => json_encode($curso->getAttributes()),
            "id_usuario_creacion" => $curso->id_usuario_creacion
        ]);
    }
}
