<?php

namespace App\Support;

final class PermissionLabel
{
    public static function label(string $name): string
    {
        $map = [
            // Categorías
            'categorias.view'   => 'Ver categorías',
            'categorias.create' => 'Crear categoría',
            'categorias.update' => 'Editar categoría',
            'categorias.delete' => 'Eliminar categoría',
            
            //areas
            'areas.view'   => 'Ver areas',
            'areas.create' => 'Crear area',
            'areas.update' => 'Editar area',
            'areas.delete' => 'Eliminar area',

            // Items
            'items.view'   => 'Ver ítems',
            'items.create' => 'Crear ítem',
            'items.update' => 'Editar ítem',
            'items.delete' => 'Eliminar ítem',

            // Devoluciones
            'devoluciones.view'   => 'Ver devoluciones',
            'devoluciones.create' => 'Registrar devolución',

            // Dotaciones
            'dotaciones.view'   => 'Ver dotaciones',
            'dotaciones.create' => 'Crear dotación',
            'dotaciones.update' => 'Editar dotación',
            'dotaciones.delete' => 'Eliminar dotación',

            // Prestamos
            'prestamos.view'   => 'Ver préstamos',
            'prestamos.create' => 'Crear préstamo',
            'prestamos.update' => 'Editar préstamo',
            'prestamos.delete' => 'Eliminar préstamo',
            'prestamos.incidentes.store' => 'Registrar incidente de préstamo',

            // Seguridad
            'permissions.view'     => 'Ver permisos',
            'roles.view'           => 'Ver roles',
            'roles.create'         => 'Crear rol',
            'roles.update'         => 'Editar rol',
            'roles.delete'         => 'Eliminar rol',
            'users.view'           => 'Ver usuarios',
            'users.assign.roles'   => 'Asignar roles a usuarios',
        ];

        return $map[$name] ?? self::humanize($name);
    }

    private static function humanize(string $name): string
    {
        return ucfirst(str_replace(
            ['.', '_'],
            [' › ', ' '],
            $name
        ));
    }
}
