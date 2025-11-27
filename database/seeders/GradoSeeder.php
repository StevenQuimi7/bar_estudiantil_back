<?php

namespace Database\Seeders;

use App\Models\grado\Grado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $grados=[
            ["grado"=>1, "id_usuario_creacion"=>1],
            ["grado"=>2, "id_usuario_creacion"=>1],
            ["grado"=>3, "id_usuario_creacion"=>1],
            ["grado"=>4, "id_usuario_creacion"=>1],
            ["grado"=>5, "id_usuario_creacion"=>1],
            ["grado"=>6, "id_usuario_creacion"=>1],
            ["grado"=>7, "id_usuario_creacion"=>1],
            ["grado"=>8, "id_usuario_creacion"=>1],
            ["grado"=>9, "id_usuario_creacion"=>1],
            ["grado"=>10, "id_usuario_creacion"=>1],
        ];
        // Grado::insert($grados);
        foreach ($grados as $item) {
            Grado::create($item);
        }
    }
}
