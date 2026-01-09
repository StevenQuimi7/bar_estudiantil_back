<?php

namespace App\Observers\categoria;

use App\Models\categoria\Categoria;

class CategoriaObserver
{
    //
    public function created(Categoria $categoria): void
    {
        //
        $categoria->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($categoria),
            "id_usuario_creacion" => $categoria->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Categoria "updated" event.
     */
    public function updated(Categoria $categoria): void
    {
        //
        if ($categoria->wasChanged('activo') && $categoria->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $categoria->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($categoria->getOriginal()),
            "data_actual"   => json_encode($categoria->getAttributes()),
            "id_usuario_creacion" => $categoria->id_usuario_creacion
        ]);
    }
}
