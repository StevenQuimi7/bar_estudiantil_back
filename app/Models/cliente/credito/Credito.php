<?php

namespace App\Models\cliente\credito;

use App\Models\Auditoria;
use App\Models\cliente\Cliente;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Credito extends ModeloBase
{
    //
   protected $table = 'credito_clientes';
    protected $fillable = [
        "id_cliente",
        "saldo",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function movimientos(){
        return $this->hasMany(CreditoMovimiento::class, 'id_credito_cliente','id');
    }
}
