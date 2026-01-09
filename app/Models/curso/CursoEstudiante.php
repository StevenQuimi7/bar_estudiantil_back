<?php

namespace App\Models\curso;

use App\Models\Auditoria;
use App\Models\estudiante\Cliente;
use App\Models\curso\Curso;
use App\Models\estudiante\Estudiante;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CursoEstudiante extends ModeloBase
{
    //
    protected $table = 'curso_estudiante';
    protected $fillable = [
        "id_estudiante",
        "id_curso",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function curso(){
        return $this->belongsTo(Curso::class, 'id_curso','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function estudiante(){
        return $this->belongsTo(Estudiante::class,'id_estudiante','id');
    }
    
}
