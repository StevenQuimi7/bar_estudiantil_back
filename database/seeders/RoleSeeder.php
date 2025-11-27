<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // $administrador=Role::insert(
        //     [
        //         ['name'=>'administrador','guard_name' => 'web','created_at'=>now()],
        //         ['name'=>'operador', 'guard_name' => 'web','created_at'=>now()]
        //     ]
        // );
        $administrador = Role::create(['name'=>'administrador','guard_name' => 'web','created_at'=>now()]);
        $operador = Role::create(['name'=>'operador', 'guard_name' => 'web','created_at'=>now()]);
        $jefe = Role::create(['name'=>'jefe', 'guard_name' => 'web','created_at'=>now()]);
        // $administrador = Role::where('name','administrador')->first();
        // $operador = Role::where('name','operador')->first();
        // ['name'=>'producto.carga_masiva', 'description'=>'Acción carga masiva productos'],
        // ['name'=>'producto.reporte_excel', 'description'=>'Acción reporte_excel productos'],
        // ['name'=>'estudiante.carga_masiva', 'description'=>'Acción carga masiva estudiantes'],
        // ['name'=>'estudiante.reporte_excel', 'description'=>'Acción reporte_excel estudiantes'],
        $permisos = [
            //permisos ventas
            ['name'=>'venta', 'description'=>'Visualizar módulo de ventas', 'guard_name'=>'web'],
            //permisos ventas diarias
            ['name'=>'venta_diarias', 'description'=>'Visualizar módulo de ventas diarias', 'guard_name'=>'web'],
            ['name'=>'venta_diaria.store', 'description'=>'Acción crear ventas diarias', 'guard_name'=>'web'],
            ['name'=>'venta_diaria.update', 'description'=>'Acción Modificar ventas diarias', 'guard_name'=>'web'],
            ['name'=>'venta_diaria.delete', 'description'=>'Acción Eliminar ventas diarias', 'guard_name'=>'web'],
            //permisos reporte de ventas semanal
            ['name'=>'generar_reporte_ventas', 'description'=>'Visualizar módulo de generar reporte ventas', 'guard_name'=>'web'],
            //permisos ventas generadas
            ['name'=>'venta_generadas', 'description'=>'Visualizar módulo de reportes de ventas generados', 'guard_name'=>'web'],
            ['name'=>'venta_generadas.update', 'description'=>'Acción modificar estado de venta generada', 'guard_name'=>'web'],
            //permisos productos
            ['name'=>'producto', 'description'=>'Visualizar módulo de productos', 'guard_name'=>'web'],
            ['name'=>'producto.store', 'description'=>'Acción crear productos', 'guard_name'=>'web'],
            ['name'=>'producto.update', 'description'=>'Acción Modificar productos', 'guard_name'=>'web'],
            ['name'=>'producto.delete', 'description'=>'Acción Eliminar productos', 'guard_name'=>'web'],
            //permisos bonos
            ['name'=>'bono', 'description'=>'Visualizar módulo de bonos', 'guard_name'=>'web'],
            ['name'=>'bono.store', 'description'=>'Acción registrar bonos', 'guard_name'=>'web'],
            ['name'=>'bono.update', 'description'=>'Acción Modificar bonos', 'guard_name'=>'web'],
            ['name'=>'bono.delete', 'description'=>'Acción Eliminar ventas', 'guard_name'=>'web'],
            //permisos estudiantes
            ['name'=>'estudiante', 'description'=>'Visualizar módulo de estudiantes', 'guard_name'=>'web'],
            ['name'=>'estudiante.store', 'description'=>'Acción crear estudiantes', 'guard_name'=>'web'],
            ['name'=>'estudiante.update', 'description'=>'Acción Modificar estudiantes', 'guard_name'=>'web'],
            ['name'=>'estudiante.delete', 'description'=>'Acción Eliminar estudiantes', 'guard_name'=>'web'],
            //permisos categorias
            ['name'=>'categoria', 'description'=>'Visualizar módulo de categorías', 'guard_name'=>'web'],
            ['name'=>'categoria.store', 'description'=>'Acción crear de categorías', 'guard_name'=>'web'],
            ['name'=>'categoria.update', 'description'=>'Acción Modificar categorías', 'guard_name'=>'web'],
            ['name'=>'categoria.delete', 'description'=>'Acción Eliminar categorías', 'guard_name'=>'web'],
            //permisos grados
            ['name'=>'grado', 'description'=>'Visualizar módulo matenimiento de grados', 'guard_name'=>'web'],
            ['name'=>'grado.store', 'description'=>'Acción Crear grados', 'guard_name'=>'web'],
            ['name'=>'grado.update', 'description'=>'Acción Modificar grados', 'guard_name'=>'web'],
            ['name'=>'grado.delete', 'description'=>'Acción Eliminar grados', 'guard_name'=>'web'],
            //permisos cursos
            ['name'=>'curso', 'description'=>'Visualizar módulo matenimiento de cursos', 'guard_name'=>'web'],
            ['name'=>'curso.store', 'description'=>'Acción Crear cursos', 'guard_name'=>'web'],
            ['name'=>'curso.update', 'description'=>'Acción Modificar cursos', 'guard_name'=>'web'],
            ['name'=>'curso.delete', 'description'=>'Acción Eliminar cursos', 'guard_name'=>'web'],
            //permisos especialidades
            ['name'=>'especialidad', 'description'=>'Visualizar módulo matenimiento de especialidades', 'guard_name'=>'web'],
            ['name'=>'especialidad.store', 'description'=>'Acción Crear especialidades', 'guard_name'=>'web'],
            ['name'=>'especialidad.update', 'description'=>'Acción Modificar especialidades', 'guard_name'=>'web'],
            ['name'=>'especialidad.delete', 'description'=>'Acción Eliminar especialidades', 'guard_name'=>'web'],
            //permisos niveles
            ['name'=>'nivel', 'description'=>'Visualizar módulo matenimiento de niveles', 'guard_name'=>'web'],
            ['name'=>'nivel.store', 'description'=>'Acción Crear niveles', 'guard_name'=>'web'],
            ['name'=>'nivel.update', 'description'=>'Acción Modificar niveles', 'guard_name'=>'web'],
            ['name'=>'nivel.delete', 'description'=>'Acción Eliminar niveles', 'guard_name'=>'web'],
            //permisos roles
            ['name'=>'role', 'description'=>'Visualizar módulo de roles', 'guard_name'=>'web'],
            ['name'=>'role.store', 'description'=>'Acción Crear roles', 'guard_name'=>'web'],
            ['name'=>'role.update', 'description'=>'Acción Modificar roles', 'guard_name'=>'web'],
            ['name'=>'role.delete', 'description'=>'Acción Eliminar roles', 'guard_name'=>'web'],
            //permisos usuarios
            ['name'=>'usuario', 'description'=>'Visualizar módulo de usuarios', 'guard_name'=>'web'],
            ['name'=>'usuario.store', 'description'=>'Acción Crear usuarios', 'guard_name'=>'web'],
            ['name'=>'usuario.update', 'description'=>'Acción Modificar usuarios', 'guard_name'=>'web'],
            ['name'=>'usuario.delete', 'description'=>'Acción Eliminar usuarios', 'guard_name'=>'web'],
        ];
        Permission::insert($permisos);

        $permisos_name = collect($permisos)->pluck('name')->toArray();

        $administrador->syncPermissions($permisos_name);
        $operador->syncPermissions($permisos_name);
        $jefe->syncPermissions($permisos_name);

        //agg permisos
        // Permission::create([
        //     'name'=>'estudiante', 'descripcion'=>'Visualizar módulo de estudiantes'
        // ])->syncRoles([$administrador,$operador])
        //permisos general

    }
}
