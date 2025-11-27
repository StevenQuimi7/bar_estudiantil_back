<?php

namespace App\Models\venta;

use App\Models\Auditoria;
use App\Models\ModeloBase;
use App\Models\producto\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends ModeloBase
{
    //
    protected $table = 'detalle_ventas';
    protected $fillable = [
        "id_venta",
        "id_producto",
        "cantidad",
        "subtotal",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];
    public function producto(){
        return $this->belongsTo(Producto::class, 'id_producto','id');
    }
    public function venta(){
        return $this->belongsTo(Venta::class, 'id_venta','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
