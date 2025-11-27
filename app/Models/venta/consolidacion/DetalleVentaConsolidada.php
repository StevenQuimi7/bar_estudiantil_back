<?php

namespace App\Models\venta\consolidacion;

use App\Models\Auditoria;
use App\Models\ModeloBase;
use App\Models\User;
use App\Models\venta\Venta;
use Illuminate\Database\Eloquent\Model;

class DetalleVentaConsolidada extends ModeloBase
{
    protected $table = 'detalle_venta_consolidadas';
    protected $fillable = [
        "id_venta_consolidada",
        "id_venta",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function venta_consoldiada(){
        return $this->belongsTo(VentaConsolidada::class, 'id_venta_consolidada','id');
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
