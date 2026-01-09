<?php

namespace Database\Seeders;

use App\Models\cliente\TipoCliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $tipo_clientes = [
            ["nombre"=>"ESTUDIANTE", "id_usuario_creacion"=>1],
            ["nombre"=>"DOCENTE", "id_usuario_creacion"=>1],
            ["nombre"=>"SISTEMAS", "id_usuario_creacion"=>1],
            ["nombre"=>"OTROS", "id_usuario_creacion"=>1],
        ];
        TipoCliente::insert($tipo_clientes);
    }
}
