<?php

namespace App\Models\categoria;

use App\Models\Auditoria;
use App\Models\Image;
use App\Models\ModeloBase;
use App\Models\producto\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Categoria extends ModeloBase
{
    //
    protected $table = 'categorias';
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

    public function productos(){
        return $this->hasMany(Producto::class, 'id_categoria','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function image(){
        return $this->morphOne(Image::class,'imageable');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
