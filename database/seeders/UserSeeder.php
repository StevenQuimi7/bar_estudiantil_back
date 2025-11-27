<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create(
            [
                "nombres"   => "Administrador",
                "apellidos" => "Administrador",
                "username"      => "admin",
                "email"     => "admin@gmail.com",
                "password"  => bcrypt("Admin2025*") 
            ])->assignRole("administrador");

        User::create(
            [
                "nombres"   => "Operador",
                "apellidos" => "uno",
                "username"      => "opeuno",
                "email"     => "opeuno@gmail.com",
                "password"  => bcrypt("opeuno2025*") 
            ])->assignRole("operador");
        User::create(
            [
                "nombres"   => "Floria",
                "apellidos" => "Lambertucci",
                "username"      => "flambertucci",
                "email"     => "flambertucci@gmail.com",
                "password"  => bcrypt("flambertucci2025*") 
            ])->assignRole("jefe");

    }
}
