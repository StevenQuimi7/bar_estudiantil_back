<?php

namespace App\Models\estudiante\bono;

use App\Models\Auditoria;
use App\Models\estudiante\Estudiante;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BonoEstudiante extends ModeloBase
{
    protected $table = 'bono_estudiantes';
    protected $fillable = [
        "id_estudiante",
        "saldo",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function estudiante(){
        return $this->belongsTo(Estudiante::class, 'id_estudiante','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function movimientos(){
        return $this->hasMany(MovimientoBono::class, 'id_bono_estudiante','id');
    }
}
