<?php

namespace App\Models\especialidad;

use App\Models\Auditoria;
use App\Models\curso\Curso;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends ModeloBase
{
    //
    protected $table = 'especialidades';
    protected $fillable = [
        "nombre",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => mb_strtoupper(trim($value),'UTF-8'),
        );
    }
    public function cursos(){
        return $this->hasMany(Curso::class,'id_especialidad','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
