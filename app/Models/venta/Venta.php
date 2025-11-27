<?php

namespace App\Models\venta;

use App\Models\Auditoria;
use App\Models\cliente\Cliente;
use App\Models\ModeloBase;
use App\Models\User;
use App\Models\venta\consolidacion\DetalleVentaConsolidada;
use App\Models\venta\consolidacion\VentaConsolidada;
use Illuminate\Database\Eloquent\Model;

class Venta extends ModeloBase
{
    //
    protected $table = 'ventas';
    protected $fillable = [
        "id_cliente",
        "total_pagar",
        "descuento_credito",
        "total_venta",
        "estado_gestion",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente','id');
    }
    public function detalles_venta(){
        return $this->hasMany(DetalleVenta::class, 'id_venta','id');
    }
    public function detalles_ventas_consolidadas(){
        return $this->hasMany(DetalleVentaConsolidada::class, 'id_venta','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
