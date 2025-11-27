<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $table = 'images';
    protected $fillable = [
        "path",
        "extension",
        "id_usuario_creacion",
        "activo"
    ];
    protected $cast = [
        "activo"=>"boolean"
    ];
    public function usuario(){
        return $this->belongsTo(User::class, 'id_usuario_creacion','id');
    }
    public function imageable(){
        return $this->morphTo();
    }
}
