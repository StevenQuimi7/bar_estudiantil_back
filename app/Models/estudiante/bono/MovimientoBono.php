<?php

namespace App\Models\estudiante\bono;

use App\Models\Auditoria;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MovimientoBono extends ModeloBase
{
    protected $table = 'movimiento_bonos';
    protected $fillable = [
        "id_bono_estudiante",
        "tipo",//entrada, salida
        "monto",
        "descripcion",
        "saldo_anterior",
        "saldo_actual",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function bono_estudiantil(){
        return $this->belongsTo(BonoEstudiante::class, 'id_bono_estudiante','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
