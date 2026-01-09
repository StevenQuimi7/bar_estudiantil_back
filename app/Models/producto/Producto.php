<?php

namespace App\Models\producto;

use App\Models\Auditoria;
use App\Models\categoria\Categoria;
use App\Models\Image;
use App\Models\ModeloBase;
use App\Models\User;
use App\Models\venta\DetalleVenta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Producto extends ModeloBase
{
    //
    protected $table = 'productos';
    protected $fillable = [
        "id_categoria",
        "codigo",
        "nombre",
        "precio",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => mb_strtoupper(trim($value),'UTF-8'),
        );
    }
    protected function codigo(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => mb_strtoupper(trim($value),'UTF-8'),
        );
    }

    public function categoria(){
        return $this->belongsTo(Categoria::class, 'id_categoria','id');
    }
    public function detalles_venta(){
        return $this->hasMany(DetalleVenta::class, 'id_producto','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function image(){
        return $this->morphOne(Image::class, 'imageable');
    }
}
