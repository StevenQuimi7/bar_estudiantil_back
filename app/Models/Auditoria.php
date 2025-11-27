<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends ModeloBase
{
    //
    protected $table = 'auditorias';
    protected $fillable = [
        "accion",
        "data_anterior",
        "data_actual",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];
    public function user(){
        return $this->belongsTo(User::class, 'id_usuario_creacion','id');
    }
    public function auditable(){
        return $this->morphTo();
    }
}
