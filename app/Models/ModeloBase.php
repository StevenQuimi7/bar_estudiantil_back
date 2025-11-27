<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeloBase extends Model
{
    //
    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }
    public function scopeInactivo($query)
    {
        return $query->where('activo', 0);
    }


}
