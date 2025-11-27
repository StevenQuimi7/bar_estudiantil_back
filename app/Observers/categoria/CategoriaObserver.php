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
        $categoria->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($categoria->getPrevious()),
            "data_actual"=>json_encode($categoria),
            "id_usuario_creacion" => $categoria->id_usuario_creacion
        ]);
    }
}
