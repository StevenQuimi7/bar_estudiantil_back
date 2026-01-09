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
        if ($cliente->wasChanged('activo') && $cliente->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $cliente->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($cliente->getOriginal()),
            "data_actual"   => json_encode($cliente->getAttributes()),
            "id_usuario_creacion" => $cliente->id_usuario_creacion
        ]);
    }
}
