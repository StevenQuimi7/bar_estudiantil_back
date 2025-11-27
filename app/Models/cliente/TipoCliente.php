<?php

namespace App\Models\cliente;

use App\Models\Auditoria;
use App\Models\ModeloBase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TipoCliente extends ModeloBase
{
    //
    protected $table = 'tipo_clientes';
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

    public function cliente(){
        return $this->hasMany(Cliente::class, 'id_tipo_cliente','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
}
