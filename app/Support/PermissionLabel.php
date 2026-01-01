<?php

namespace App\Support;

final class PermissionLabel
{
    public static function label(string $name): string
    {
        $map = [
            // Seguridad y Accesos
            'permissions.view'     => 'Ver permisos',
            'permissions.create'   => 'Crear permisos',
            'permissions.update'   => 'Editar permisos',
            'permissions.delete'   => 'Eliminar permisos',
            'roles.view'           => 'Ver roles',
            'roles.create'         => 'Crear rol',
            'roles.update'         => 'Editar rol',
            'roles.delete'         => 'Eliminar rol',
            'users.view'           => 'Ver usuarios',
            'users.assign.roles'   => 'Asignar roles a usuarios',

            // Datos Maestros
            'personas.view'        => 'Ver personas',
            'personas.create'      => 'Crear persona',
            'personas.update'      => 'Editar persona',
            'personas.delete'      => 'Eliminar persona',
            'sucursal.view'        => 'Ver sucursales',
            'sucursal.create'      => 'Crear sucursal',
            'sucursal.update'      => 'Editar sucursal',
            'sucursal.delete'      => 'Eliminar sucursal',
            'proyectos.view'       => 'Ver proyectos',
            'proyectos.create'     => 'Crear proyecto',
            'proyectos.update'     => 'Editar proyecto',
            'proyectos.delete'     => 'Eliminar proyecto',
            'areas.view'           => 'Ver áreas',
            'areas.create'         => 'Crear área',
            'areas.update'         => 'Editar área',
            'areas.delete'         => 'Eliminar área',
            'categorias.view'      => 'Ver categorías',
            'categorias.create'    => 'Crear categoría',
            'categorias.update'    => 'Editar categoría',
            'categorias.delete'    => 'Eliminar categoría',
            'medidas.view'         => 'Ver unidades de medida',
            'medidas.create'       => 'Crear unidad de medida',
            'medidas.update'       => 'Editar unidad de medida',
            'medidas.delete'       => 'Eliminar unidad de medida',

            // Inventario y Kits
            'items.view'           => 'Ver ítems',
            'items.create'         => 'Crear ítem',
            'items.update'         => 'Editar ítem',
            'items.delete'         => 'Eliminar ítem',
            'kits.view'            => 'Ver kits de emergencia',
            'kits.create'          => 'Crear kit de emergencia',
            'kits.update'          => 'Editar kit de emergencia',
            'kits.delete'          => 'Eliminar kit de emergencia',
            'movimientos.view'     => 'Ver historial de movimientos',

            // Préstamos y Devoluciones
            'prestamos.view'       => 'Ver préstamos',
            'prestamos.create'     => 'Crear préstamo',
            'prestamos.update'     => 'Editar préstamo',
            'prestamos.delete'     => 'Eliminar préstamo',
            'prestamos.imprimir'   => 'Imprimir recibos de préstamo',
            'devoluciones.view'    => 'Ver devoluciones',
            'devoluciones.create'  => 'Registrar devolución',
            'devoluciones.anular'  => 'Anular devolución',
            'devoluciones.kit'     => 'Gestionar devoluciones de kits',

            // Dotaciones e Incidentes
            'dotaciones.view'      => 'Ver dotaciones',
            'dotaciones.create'    => 'Crear dotación',
            'dotaciones.update'    => 'Editar dotación',
            'dotaciones.delete'    => 'Eliminar dotación',
            'incidentes.view'      => 'Ver incidentes',
            'incidentes.create'    => 'Registrar incidente',
            'incidentes.update'    => 'Editar incidente',
            'incidentes.delete'    => 'Eliminar incidente',
            'incidentes.devolver'  => 'Procesar devolución de incidente',
            'incidentes.completar' => 'Finalizar caso de incidente',

            // Compras y Consumos
            'compras.view'         => 'Ver compras',
            'compras.create'       => 'Crear solicitud de compra',
            'compras.update'       => 'Editar compra',
            'compras.delete'       => 'Eliminar compra',
            'compras.resolver'     => 'Resolver/Cerrar compra',
            'consumos.view'        => 'Ver reporte de consumos',
            'consumos.pdf'         => 'Exportar reporte de consumos a PDF',
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
