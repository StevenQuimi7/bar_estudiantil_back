<?php

namespace Database\Seeders;

use App\Models\nivel\Nivel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $niveles = [
            ["nombre"=>"BÁSICA", "id_usuario_creacion"=>1],
            ["nombre"=>"BÁSICA SUPERIOR", "id_usuario_creacion"=>1],
            ["nombre"=>"BACHILLERATO", "id_usuario_creacion"=>1],
        ];
        // Nivel::insert($niveles);
        foreach ($niveles as $item) {
            Nivel::create($item);
        }
    }
}
