<?php

namespace App\Models\cliente\credito;

use App\Models\Auditoria;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CreditoMovimiento extends ModeloBase
{
    protected $table = 'movimiento_creditos';
    protected $fillable = [
        "id_credito_cliente",
        "tipo",//consumo, reverso, abono
        "monto",
        "descripcion",
        "saldo_anterior",
        "id_venta",
        "id_venta_consolidada",
        "saldo_actual",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function credito(){
        return $this->belongsTo(Credito::class, 'id_credito_cliente','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
