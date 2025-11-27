<?php

namespace App\Models\estudiante;

use App\Models\Auditoria;
use App\Models\curso\Curso;
use App\Models\estudiante\bono\BonoEstudiante;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends ModeloBase
{
    //
    protected $table = 'estudiantes';
    protected $fillable = [
        "id_curso",
        "primer_nombre",
        "segundo_nombre",
        "primer_apellido",
        "segundo_apellido",
        "numero_identificacion",
        "edad",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    protected $appends = ['nombres', 'apellidos'];
    protected function primerNombre(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) =>  mb_strtoupper(trim($value),'UTF-8'),
        );
    }

    protected function segundoNombre(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) =>  mb_strtoupper(trim($value),'UTF-8'),
        );
    }
    protected function primerApellido(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) =>  mb_strtoupper(trim($value),'UTF-8'),
        );
    }
    protected function segundoApellido(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) =>  mb_strtoupper(trim($value),'UTF-8'),
        );
    }

    public function getNombresAttribute()
    {
        return "{$this->primer_nombre} {$this->segundo_nombre}";
    }
    public function getApellidosAttribute()
    {
        return "{$this->primer_apellido} {$this->segundo_apellido}";
    }

    public function curso(){
        return $this->belongsTo(Curso::class, 'id_curso','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function bono(){
        return $this->hasOne(BonoEstudiante::class,'id_estudiante','id');
    }
    
}
