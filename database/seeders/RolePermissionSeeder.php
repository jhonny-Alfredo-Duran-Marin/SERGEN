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
        // ===== Permisos del sistema =====
        $perms = [
            // Permisos
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            // Usuarios
            'users.view',
            'users.assign.roles',

            // Personas
            'personas.view',
            'personas.create',
            'personas.update',
            'personas.delete',

            // Proyectos
            'proyectos.view',
            'proyectos.create',
            'proyectos.update',
            'proyectos.delete',

            // Áreas
            'areas.view',
            'areas.create',
            'areas.update',
            'areas.delete',

            // Categorías
            'categorias.view',
            'categorias.create',
            'categorias.update',
            'categorias.delete',

            // Medidas
            'medidas.view',
            'medidas.create',
            'medidas.update',
            'medidas.delete',

            // Ítems
            'items.view',
            'items.create',
            'items.update',
            'items.delete',

            // Movimientos (solo lectura)
            'movimientos.view',

            // Préstamos
            'prestamos.view',
            'prestamos.create',
            'prestamos.update',
            'prestamos.delete',

            // Devoluciones
            'devoluciones.view',
            'devoluciones.create',
            // Permiso opcional específico para devolver kit completo:
            'devoluciones.kit',

            // Dotaciones
            'dotaciones.view',
            'dotaciones.create',
            'dotaciones.update',
            'dotaciones.delete',

            // Incidentes en préstamo
            'prestamos.incidentes.store',

            // Kits de emergencia
            'kits.view',
            'kits.create',
            'kits.update',
            'kits.delete',

            // Compras (faltaban)
            'compras.view',
            'compras.create',
            'compras.update',
            'compras.delete',

            'sucursal.view',
            'sucursal.create',
            'sucursal.update',
            'sucursal.delete',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ===== Roles =====
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'Admin',      'guard_name' => 'web']);
        $editor     = Role::firstOrCreate(['name' => 'Editor',     'guard_name' => 'web']);

        // SuperAdmin y Admin con todo (puedes ajustar Admin si quieres)
        $all = Permission::all();
        $superAdmin->syncPermissions($all);
        $admin->syncPermissions($all);

        // Editor solo lectura básica (ajusta a gusto)
        $editor->syncPermissions([
            'roles.view', 'permissions.view', 'users.view',
            'personas.view', 'proyectos.view', 'areas.view', 'categorias.view',
            'medidas.view', 'items.view', 'movimientos.view',
            'prestamos.view', 'devoluciones.view', 'dotaciones.view',
            'kits.view', 'compras.view',
        ]);

        // ===== Usuario SuperAdmin =====
        $saName  = env('SUPERADMIN_NAME', 'Super Admin');
        $saEmail = env('SUPERADMIN_EMAIL', 'superadmin@example.com');
        $saPass  = env('SUPERADMIN_PASSWORD', default: '123456789');

        $superUser = \App\Models\User::updateOrCreate(
            ['email' => $saEmail],
            [
                'name'              => $saName,
                'password'          => $saPass, // usando cast 'hashed'
                'email_verified_at' => now(),
            ]
        );
        $superUser->syncRoles(['SuperAdmin']);

        $this->command->info('Permisos y roles creados/actualizados.');
        $this->command->info("SuperAdmin: {$saEmail} listo.");
    }
}
