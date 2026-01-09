<?php

namespace App\Observers\estudiante;

use App\Models\estudiante\Estudiante;

class EstudianteObserver
{
    //
    public function created(Estudiante $estudiante): void
    {
        //
        $estudiante->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($estudiante),
            "id_usuario_creacion" => $estudiante->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Estudiante "updated" event.
     */
    public function updated(Estudiante $estudiante): void
    {
        //
        if ($estudiante->wasChanged('activo') && $estudiante->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }


        $estudiante->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($estudiante->getOriginal()),
            "data_actual"   => json_encode($estudiante->getAttributes()),
            "id_usuario_creacion" => $estudiante->id_usuario_creacion
        ]);
    }
}
