<?php

namespace App\Models\grado;

use App\Models\Auditoria;
use App\Models\curso\Curso;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Grado extends ModeloBase
{
    //
    protected $table = 'grados';
    protected $fillable = [
        "grado",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];
    public function cursos(){
        return $this->hasMany(Curso::class,'id_grado','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
