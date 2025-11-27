<?php

namespace App\Models\curso;

use App\Models\Auditoria;
use App\Models\especialidad\Especialidad;
use App\Models\estudiante\Estudiante;
use App\Models\grado\Grado;
use App\Models\ModeloBase;
use App\Models\nivel\Nivel;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Curso extends ModeloBase
{
    //
    protected $table = 'cursos';
    protected $fillable = [
        "id_grado",
        "id_nivel",
        "seccion",
        "id_especialidad",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    protected function seccion(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper(trim($value)),
        );
    }

    public function grado(){
        return $this->belongsTo(Grado::class,'id_grado','id');
    }
    public function estudiante(){
        return $this->hasMany(Estudiante::class,'id_estudiante','id');
    }
    public function nivel(){
        return $this->belongsTo(Nivel::class,'id_nivel','id');
    }
    public function especialidad(){
        return $this->belongsTo(Especialidad::class,'id_especialidad','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function curso_estudiante(){
        return $this->hasMany(CursoEstudiante::class,'id_curso','id');
    }
}
