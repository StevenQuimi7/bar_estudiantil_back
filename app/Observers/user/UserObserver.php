<?php

namespace App\Observers\user;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    //
    public function created(User $user): void
    {
        //
        $user->auditoria()->create([
            "accion" => "CREAR",
            "data_anterior"=>null,
            "data_actual"=>json_encode($user),
            "id_usuario_creacion" => optional($user)->id_usuario_creacion ?? 1
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
        if ($user->wasChanged('activo') && $user->activo == 0) {
            $accion = 'ELIMINAR';
        } else {
            $accion = 'ACTUALIZAR';
        }

        $user->auditoria()->create([
            "accion" => $accion,
            "data_anterior" => json_encode($user->getOriginal()),
            "data_actual"   => json_encode($user->getAttributes()),
            "id_usuario_creacion" => $user?->id_usuario_creacion ?? 1
        ]);
    }
}
