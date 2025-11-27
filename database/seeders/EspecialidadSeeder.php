<?php

namespace Database\Seeders;

use App\Models\especialidad\Especialidad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $especialidades = [
            ["nombre"=>"TECNICO", "id_usuario_creacion"=>1],
            ["nombre"=>"INFORMATICA", "id_usuario_creacion"=>1],
            ["nombre"=>"CONTABILIDAD", "id_usuario_creacion"=>1],
            ["nombre"=>"CIENCIAS", "id_usuario_creacion"=>1]
        ];
        // Especialidad::insert($especialidades);
        foreach ($especialidades as $item) {
            Especialidad::create($item);
        }
    }
}
