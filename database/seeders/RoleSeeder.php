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
        //         ['name'=>'administrador','guard_name' => 'api','created_at'=>now()],
        //         ['name'=>'operador', 'guard_name' => 'api','created_at'=>now()]
        //     ]
        // );
        $administrador = Role::create(['name'=>'administrador','guard_name' => 'api','created_at'=>now()]);
        $operador = Role::create(['name'=>'operador', 'guard_name' => 'api','created_at'=>now()]);
        $jefe = Role::create(['name'=>'jefe', 'guard_name' => 'api','created_at'=>now()]);
        // $administrador = Role::where('name','administrador')->first();
        // $operador = Role::where('name','operador')->first();
        // ['name'=>'producto.carga_masiva', 'description'=>'Acción carga masiva productos'],
        // ['name'=>'producto.reporte_excel', 'description'=>'Acción reporte_excel productos'],
        // ['name'=>'estudiante.carga_masiva', 'description'=>'Acción carga masiva estudiantes'],
        // ['name'=>'estudiante.reporte_excel', 'description'=>'Acción reporte_excel estudiantes'],
        $permisos = [
            //permisos ventas
            ['name'=>'venta', 'description'=>'Visualizar módulo de ventas', 'guard_name'=>'api'],
            ['name'=>'venta.store', 'description'=>'Acción crear ventas', 'guard_name'=>'api'],
            ['name'=>'venta.update', 'description'=>'Acción Modificar ventas', 'guard_name'=>'api'],
            ['name'=>'venta.delete', 'description'=>'Acción Eliminar ventas', 'guard_name'=>'api'],
            ['name'=>'venta.exportar', 'description'=>'Acción Exportar ventas', 'guard_name'=>'api'],
            ['name'=>'venta.updateEstadoGestion', 'description'=>'Acción Actualizar Estado Gestion ventas', 'guard_name'=>'api'],
            //permisos productos
            ['name'=>'producto', 'description'=>'Visualizar módulo de productos', 'guard_name'=>'api'],
            ['name'=>'producto.store', 'description'=>'Acción crear productos', 'guard_name'=>'api'],
            ['name'=>'producto.update', 'description'=>'Acción Modificar productos', 'guard_name'=>'api'],
            ['name'=>'producto.delete', 'description'=>'Acción Eliminar productos', 'guard_name'=>'api'],
            ['name'=>'producto.exportar', 'description'=>'Acción Exportar productos', 'guard_name'=>'api'],
            ['name'=>'producto.cargaMasiva', 'description'=>'Acción Carga Masiva productos', 'guard_name'=>'api'],
            ['name'=>'producto.descargarPlantilla', 'description'=>'Acción Descargar plantilla productos', 'guard_name'=>'api'],
            //permisos bonos
            ['name'=>'credito.creditoCliente', 'description'=>'Visualizar módulo de crédito', 'guard_name'=>'api'],
            ['name'=>'credito.store', 'description'=>'Acción registrar crédito', 'guard_name'=>'api'],
            ['name'=>'credito.movimientos', 'description'=>'Acción Ver crédito movimientos', 'guard_name'=>'api'],
            //permisos estudiantes
            ['name'=>'estudiante', 'description'=>'Visualizar módulo de estudiantes', 'guard_name'=>'api'],
            ['name'=>'estudiante.store', 'description'=>'Acción crear estudiantes', 'guard_name'=>'api'],
            ['name'=>'estudiante.update', 'description'=>'Acción Modificar estudiantes', 'guard_name'=>'api'],
            ['name'=>'estudiante.delete', 'description'=>'Acción Eliminar estudiantes', 'guard_name'=>'api'],
            //permisos categorias
            ['name'=>'categoria', 'description'=>'Visualizar módulo de categorías', 'guard_name'=>'api'],
            ['name'=>'categoria.store', 'description'=>'Acción crear de categorías', 'guard_name'=>'api'],
            ['name'=>'categoria.update', 'description'=>'Acción Modificar categorías', 'guard_name'=>'api'],
            ['name'=>'categoria.delete', 'description'=>'Acción Eliminar categorías', 'guard_name'=>'api'],
            //permisos grados
            ['name'=>'grado', 'description'=>'Visualizar módulo matenimiento de grados', 'guard_name'=>'api'],
            ['name'=>'grado.store', 'description'=>'Acción Crear grados', 'guard_name'=>'api'],
            ['name'=>'grado.update', 'description'=>'Acción Modificar grados', 'guard_name'=>'api'],
            ['name'=>'grado.delete', 'description'=>'Acción Eliminar grados', 'guard_name'=>'api'],
            //permisos cursos
            ['name'=>'curso', 'description'=>'Visualizar módulo matenimiento de cursos', 'guard_name'=>'api'],
            ['name'=>'curso.store', 'description'=>'Acción Crear cursos', 'guard_name'=>'api'],
            ['name'=>'curso.update', 'description'=>'Acción Modificar cursos', 'guard_name'=>'api'],
            ['name'=>'curso.delete', 'description'=>'Acción Eliminar cursos', 'guard_name'=>'api'],
            //permisos especialidades
            ['name'=>'especialidad', 'description'=>'Visualizar módulo matenimiento de especialidades', 'guard_name'=>'api'],
            ['name'=>'especialidad.store', 'description'=>'Acción Crear especialidades', 'guard_name'=>'api'],
            ['name'=>'especialidad.update', 'description'=>'Acción Modificar especialidades', 'guard_name'=>'api'],
            ['name'=>'especialidad.delete', 'description'=>'Acción Eliminar especialidades', 'guard_name'=>'api'],
            //permisos niveles
            ['name'=>'nivel', 'description'=>'Visualizar módulo matenimiento de niveles', 'guard_name'=>'api'],
            ['name'=>'nivel.store', 'description'=>'Acción Crear niveles', 'guard_name'=>'api'],
            ['name'=>'nivel.update', 'description'=>'Acción Modificar niveles', 'guard_name'=>'api'],
            ['name'=>'nivel.delete', 'description'=>'Acción Eliminar niveles', 'guard_name'=>'api'],
            //permisos roles
            ['name'=>'role', 'description'=>'Visualizar módulo de roles', 'guard_name'=>'api'],
            ['name'=>'role.store', 'description'=>'Acción Crear roles', 'guard_name'=>'api'],
            ['name'=>'role.update', 'description'=>'Acción Modificar roles', 'guard_name'=>'api'],
            ['name'=>'role.delete', 'description'=>'Acción Eliminar roles', 'guard_name'=>'api'],
            //permisos usuarios
            ['name'=>'usuario', 'description'=>'Visualizar módulo de usuarios', 'guard_name'=>'api'],
            ['name'=>'usuario.store', 'description'=>'Acción Crear usuarios', 'guard_name'=>'api'],
            ['name'=>'usuario.update', 'description'=>'Acción Modificar usuarios', 'guard_name'=>'api'],
            ['name'=>'usuario.delete', 'description'=>'Acción Eliminar usuarios', 'guard_name'=>'api'],
            //permisos clientes
            ['name'=>'cliente', 'description'=>'Visualizar módulo de clientes', 'guard_name'=>'api'],
            ['name'=>'cliente.store', 'description'=>'Acción Crear clientes', 'guard_name'=>'api'],
            ['name'=>'cliente.update', 'description'=>'Acción Modificar clientes', 'guard_name'=>'api'],
            ['name'=>'cliente.delete', 'description'=>'Acción Eliminar clientes', 'guard_name'=>'api'],
            ['name'=>'cliente.exportar', 'description'=>'Acción Exportar clientes', 'guard_name'=>'api'],
            ['name'=>'cliente.cargaMasiva', 'description'=>'Acción Carga Masiva clientes', 'guard_name'=>'api'],
            ['name'=>'cliente.descargarPlantilla', 'description'=>'Acción Descargar plantilla clientes', 'guard_name'=>'api'],
            //permisos documentos
            ['name'=>'documento', 'description'=>'Acción Guardor documento', 'guard_name'=>'api'],
            //permisos auditorias
            ['name'=>'auditoria', 'description'=>'Visualizar módulo de auditoria', 'guard_name'=>'api'],
            //permisos dashboard
            ['name'=>'dashboard', 'description'=>'Visualizar módulo de dashboard', 'guard_name'=>'api'],
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
