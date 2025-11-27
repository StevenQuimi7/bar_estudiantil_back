<?php

namespace App\Models\cliente;

use App\Models\Auditoria;
use App\Models\cliente\credito\Credito;
use App\Models\curso\CursoEstudiante;
use App\Models\ModeloBase;
use App\Models\User;
use App\Models\venta\consolidacion\VentaConsolidada;
use App\Models\venta\Venta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Cliente extends ModeloBase
{
    //
    protected $table = 'clientes';
    protected $fillable = [
        "id_tipo_cliente",
        "nombres",
        "apellidos",
        "numero_identificacion",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];

    protected $appends = ['nombre_completo'];
    protected function nombres(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? mb_strtoupper(trim($value),  'UTF-8') : null,
        );
    }

    protected function apellidos(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? mb_strtoupper(trim($value),  'UTF-8') : null,
        );
    }

    // protected function primerApellido(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => strtoupper(trim($value)),
    //     );
    // }
    // protected function segundoApellido(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => strtoupper(trim($value)),
    //     );
    // }

    public function getNombreCompletoAttribute()
    {
        $apellidos = $this->apellidos ?? '';
        $nombres = $this->nombres ?? '';

        return trim("{$apellidos} {$nombres}");
    }
    // public function getApellidosAttribute()
    // {
    //     return "{$this->primer_apellido} {$this->segundo_apellido}";
    // }

    public function user(){
        return $this->belongsTo(User::class,'id_usuario_creacion','id');
    }
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function credito(){
        return $this->hasOne(Credito::class,'id_cliente','id');
    }
    public function tipo_cliente(){
        return $this->belongsTo(TipoCliente::class,'id_tipo_cliente','id');
    }
    public function ventas(){
        return $this->hasMany(Venta::class,'id_cliente','id');
    }
    public function ventas_consolidadas(){
        return $this->hasMany(VentaConsolidada::class,'id_cliente','id');
    }
    public function estudiante_curso(){
        return $this->hasMany(CursoEstudiante::class,'id_cliente','id');
    }
    
}
