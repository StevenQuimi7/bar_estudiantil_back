<?php

namespace App\Observers\cliente;

use App\Models\cliente\Cliente;

class ClienteObserver
{
    //
    public function created(Cliente $cliente): void
    {
        //
        $cliente->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($cliente),
            "id_usuario_creacion" => $cliente->id_usuario_creacion
        ]);
    }

    /**
     * Handle the Cliente "updated" event.
     */
    public function updated(Cliente $cliente): void
    {
        //
        $cliente->auditoria()->create([
            "accion" => "ACTUALIZAR",
            "data_anterior"=>json_encode($cliente->getPrevious()),
            "data_actual"=>json_encode($cliente),
            "id_usuario_creacion" => $cliente->id_usuario_creacion
        ]);
    }
}
