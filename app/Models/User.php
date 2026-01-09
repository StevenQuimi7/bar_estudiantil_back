<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\categoria\Categoria;
use App\Models\curso\Curso;
use App\Models\especialidad\Especialidad;
use App\Models\estudiante\Estudiante;
use App\Models\grado\Grado;
use App\Models\nivel\Nivel;
use App\Models\producto\Producto;
use App\Models\venta\DetalleVenta;
use App\Models\venta\Venta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'username',
        'email',
        'password',
        'activo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $guard_name = 'api';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function estudiantes(){
        return $this->hasMany(Estudiante::class,'id_usuario_creacion','id');
    }
    public function productos(){
        return $this->hasMany(Producto::class,'id_usuario_creacion','id');
    }
    public function ventas(){
        return $this->hasMany(Venta::class,'id_usuario_creacion','id');
    }
    public function detalles_ventas(){
        return $this->hasMany(DetalleVenta::class,'id_usuario_creacion','id');
    }
    public function categorias(){
        return $this->hasMany(Categoria::class,'id_usuario_creacion','id');
    }
    public function grados(){
        return $this->hasMany(Grado::class,'id_usuario_creacion','id');
    }
    public function niveles(){
        return $this->hasMany(Nivel::class,'id_usuario_creacion','id');
    }
    public function cursos(){
        return $this->hasMany(Curso::class,'id_usuario_creacion','id');
    }
    public function especialidades(){
        return $this->hasMany(Especialidad::class,'id_usuario_creacion','id');
    }
    /*public function auditorias(){
        return $this->hasMany(Auditoria::class,'id_usuario_creacion','id');
    }*/
    public function auditoria(){
        return $this->morphMany(Auditoria::class, 'auditable');
    }
    public function imagenes(){
        return $this->hasMany(Image::class,'id_usuario_creacion','id');
    }

}
