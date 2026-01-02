<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Lista Maestra de Permisos =====
        $perms = [
            // Configuración de Accesos (Roles/Permisos/Usuarios)
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'users.view',
            'users.assign.roles',

            // Datos Maestros
            'personas.view',
            'personas.create',
            'personas.update',
            'personas.delete',
            'sucursal.view',
            'sucursal.create',
            'sucursal.update',
            'sucursal.delete',
            'proyectos.view',
            'proyectos.create',
            'proyectos.update',
            'proyectos.delete',
            'areas.view',
            'areas.create',
            'areas.update',
            'areas.delete',
            'ubicaciones.view',
            'ubicaciones.create',
            'ubicaciones.update',
            'ubicaciones.delete',
            'categorias.view',
            'categorias.create',
            'categorias.update',
            'categorias.delete',
            'medidas.view',
            'medidas.create',
            'medidas.update',
            'medidas.delete',

            // Inventario
            'items.view',
            'items.create',
            'items.update',
            'items.delete',
            'kits.view',
            'kits.create',
            'kits.update',
            'kits.delete',
            'movimientos.view',

            // Operaciones: Préstamos y Devoluciones
            'prestamos.view',
            'prestamos.create',
            'prestamos.update',
            'prestamos.delete',
            'prestamos.imprimir',
            'devoluciones.view',
            'devoluciones.create',
            'devoluciones.anular',
            'devoluciones.kit', // Permiso especial para lógica de kits

            // Operaciones: Dotaciones e Incidentes
            'dotaciones.view',
            'dotaciones.create',
            'dotaciones.update',
            'dotaciones.delete',
            'incidentes.view',
            'incidentes.create',
            'incidentes.update',
            'incidentes.delete',
            'incidentes.devolver',
            'incidentes.completar',

            // Operaciones: Compras y Consumos
            'compras.view',
            'compras.create',
            'compras.update',
            'compras.delete',
            'compras.resolver',
            'consumos.view',
            'consumos.pdf',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ===== Definición de Roles =====
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'Admin',      'guard_name' => 'web']);
        $editor     = Role::firstOrCreate(['name' => 'Editor',     'guard_name' => 'web']);

        // SuperAdmin y Admin obtienen todo
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);
        $admin->syncPermissions($allPermissions);

        // Editor: Solo lectura y operaciones básicas (sin borrar ni gestionar accesos)
        $editor->syncPermissions([
            'personas.view',
            'personas.create',
            'proyectos.view',
            'areas.view',
            'categorias.view',
            'medidas.view',
            'sucursal.view',
            'items.view',
            'kits.view',
            'movimientos.view',
            'prestamos.view',
            'prestamos.create',
            'prestamos.imprimir',
            'devoluciones.view',
            'devoluciones.create',
            'dotaciones.view',
            'dotaciones.create',
            'incidentes.view',
            'compras.view',
            'consumos.view'
        ]);

        // ===== Creación del Usuario Inicial =====
        $saEmail = env('SUPERADMIN_EMAIL', 'superadmin@example.com');
        $superUser = User::updateOrCreate(
            ['email' => $saEmail],
            [
                'name'              => env('SUPERADMIN_NAME', 'Super Admin'),
                'password'          => bcrypt(env('SUPERADMIN_PASSWORD', '123456789')),
                'email_verified_at' => now(),
            ]
        );
        $superUser->syncRoles(['SuperAdmin']);

        $this->command->info('Seed completado: Roles y permisos sincronizados correctamente.');
    }
}
