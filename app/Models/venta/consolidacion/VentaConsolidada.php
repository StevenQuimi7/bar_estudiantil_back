<?php

namespace App\Models\venta\consolidacion;

use App\Models\Auditoria;
use App\Models\cliente\Cliente;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VentaConsolidada extends ModeloBase
{
    protected $table = 'venta_consolidadas';
    protected $fillable = [
        "id_cliente",
        "total_pagar",
        "total_venta",
        "descuento_credito",
        "fecha_inicio",
        "fecha_fin",
        "estado_gestion",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function detalles_ventas_consolidadas(){
        return $this->hasMany(DetalleVentaConsolidada::class, 'id_venta_consolidada','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente','id');
    }
    
}
