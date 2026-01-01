<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Models\DetallePrestamo;
use App\Models\Item;
use App\Models\Medida;
use App\Models\Movimiento;
use App\Models\Persona;
use App\Models\Prestamo;
use App\Models\Proyecto;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('> SuperDemoSeeder iniciando…');

        // 1) Roles & Permisos (si existe tu seeder de roles)
        if (class_exists(RolePermissionSeeder::class)) {
            $this->call(RolePermissionSeeder::class);
        }

        // 2) Personas + Usuarios
        $this->seedUsuariosPersonas();

        // 3) Sucursales
        $this->seedSucursales();

        // 4) Catálogos (Categorías, Medidas, Áreas)
        $this->seedCatalogos();

        // 5) Proyectos
        $this->seedProyectos();

        // 6) Items con stock (incluye area_id y permite_devolucion)
        $this->seedItems();

        // 7) Préstamos + movimientos + devoluciones robustas
       // $this->seedPrestamosConMovimientos();

        $this->command->info('> SuperDemoSeeder listo ✅');
    }

    /* ====================== BLOQUES ====================== */

    private function seedUsuariosPersonas(): void
    {
        $this->command->warn('  - Personas/Usuarios…');

        // SuperAdmin desde .env
        $saName  = env('SUPERADMIN_NAME', default: 'Super Admin');
        $saEmail = env('SUPERADMIN_EMAIL', 'superadmin@example.com');
        $saPass  = env('SUPERADMIN_PASSWORD', '123456789');

        $saPersona = Persona::updateOrCreate(
            ['celular' => '70000000'],
            ['nombre' => $saName, 'cargo' => 'Director', 'estado' => true]
        );

        $super = User::updateOrCreate(
            ['email' => $saEmail],
            [
                'name'              => $saName,
                // Si tienes cast 'password' => 'hashed', NO uses Hash::make
                'password'          => $saPass,
                'persona_id'        => $saPersona->id,
                'email_verified_at' => now(),
            ]
        );

        if (method_exists($super, 'syncRoles')) {
            $super->syncRoles(['SuperAdmin']);
        }

        // Algunos Admin
        $datos = [
            ['Ana Romero', 'ana.romero@example.com', 'Gerente de Obra'],
            ['Luis Gutiérrez', 'luis.gtz@example.com', 'Jefe de Almacén'],
            ['Carla Pérez', 'carla.pz@example.com', 'Técnica'],
        ];

        foreach ($datos as [$n, $e, $cargo]) {
            $p = Persona::updateOrCreate(
                ['celular' => $this->fakePhone()],
                ['nombre' => $n, 'cargo' => $cargo, 'estado' => true]
            );

            $u = User::updateOrCreate(
                ['email' => $e],
                [
                    'name'              => $n,
                    'password'          => '123456789',
                    'persona_id'        => $p->id,
                    'email_verified_at' => now(),
                ]
            );

            if (method_exists($u, 'syncRoles')) {
                $u->syncRoles(['Admin']);
            }
        }

        // Personal sin usuario
        for ($i = 0; $i < 7; $i++) {
            Persona::updateOrCreate(
                ['celular' => $this->fakePhone()],
                [
                    'nombre' => fake()->name(),
                    'cargo'  => fake()->randomElement(['Operario', 'Supervisor', 'Técnico']),
                    'estado' => true,
                ]
            );
        }
    }

    private function seedSucursales(): void
    {
        $this->command->warn('  - Sucursales…');

        $sucursales = [
            ['descripcion' => 'Casa Matriz',        'estado' => 'Activo'],
            ['descripcion' => 'Sucursal Norte',     'estado' => 'Activo'],
            ['descripcion' => 'Sucursal Sur',       'estado' => 'Activo'],
            ['descripcion' => 'Sucursal en Receso', 'estado' => 'Pasivo'],
        ];

        foreach ($sucursales as $s) {
            Sucursal::firstOrCreate(
                ['descripcion' => $s['descripcion']],
                ['estado' => $s['estado']]
            );
        }
    }

    private function seedCatalogos(): void
    {
        $this->command->warn('  - Catálogos (Categorías, Medidas, Áreas)…');

        // CATEGORÍAS
        foreach (['Herramientas', 'Materiales', 'Electricidad', 'Plomería', 'Pinturas', 'EPP', 'Mecánica'] as $c) {
            Categoria::firstOrCreate(['descripcion' => $c], ['estado' => 'Activo']);
        }

        // MEDIDAS
        $meds = [
            ['Unidad', 'u'],
            ['Metro', 'm'],
            ['Centímetro', 'cm'],
            ['Milímetro', 'mm'],
            ['Kilogramo', 'kg'],
            ['Gramo', 'g'],
            ['Litro', 'L'],
            ['Mililitro', 'mL'],
            ['Pulgada', 'in'],
            ['Galón', 'gal'],
        ];

        foreach ($meds as [$desc, $sim]) {
            Medida::firstOrCreate(['simbolo' => $sim], ['descripcion' => $desc]);
        }

        // ÁREAS ligadas a alguna sucursal
        $sucursales = Sucursal::all();

        if ($sucursales->isEmpty()) {
            $this->command->warn('    ! No había sucursales, creando una por defecto…');
            $sucursales = collect([
                Sucursal::create([
                    'descripcion' => 'Casa Matriz',
                    'estado'      => 'Activo',
                ]),
            ]);
        }

        $areasDemo = [
            'Depósito Herramientas',
            'Depósito Materiales',
            'Área Electricidad',
            'Área Plomería',
            'Área Pinturas',
            'Almacén A',
            'Almacén B',
        ];

        foreach ($areasDemo as $a) {
            Area::firstOrCreate(
                ['descripcion' => $a],
                [
                    'estado'      => 'Activo',
                    'sucursal_id' => $sucursales->random()->id,
                ]
            );
        }
    }

    private function seedProyectos(): void
    {
        $this->command->warn('  - Proyectos…');

        for ($i = 1; $i <= 6; $i++) {
            Proyecto::firstOrCreate(
                ['codigo' => sprintf('PRJ-%03d', $i)],
                [
                    'descripcion'  => fake()->sentence(3),
                    'empresa'      => fake()->company(),
                    'orden_compra' => 'OC-' . fake()->numberBetween(1000, 9999),
                    'sitio'        => fake()->city(),
                    'monto'        => fake()->randomFloat(2, 10000, 800000),
                    'estado'       => fake()->randomElement(['Abierto', 'Cerrado']),
                    'fecha_inicio' => fake()->dateTimeBetween('-5 months', '-2 months')->format('Y-m-d'),
                    'fecha_fin'    => fake()->dateTimeBetween('-1 months', '+2 months')->format('Y-m-d'),
                ]
            );
        }
    }

    private function seedItems(): void
    {
        $this->command->warn('  - Items con stock…');

        // Categorías aseguradas
        $catHerr = Categoria::firstOrCreate(['descripcion' => 'Herramientas'], ['estado' => 'Activo']);
        $catMat  = Categoria::firstOrCreate(['descripcion' => 'Materiales'],   ['estado' => 'Activo']);
        $catElec = Categoria::firstOrCreate(['descripcion' => 'Electricidad'], ['estado' => 'Activo']);
        $catPlom = Categoria::firstOrCreate(['descripcion' => 'Plomería'],     ['estado' => 'Activo']);
        $catPint = Categoria::firstOrCreate(['descripcion' => 'Pinturas'],     ['estado' => 'Activo']);

        // Medidas
        $medU  = Medida::where('simbolo', 'u')->firstOrFail();
        $medM  = Medida::where('simbolo', 'm')->firstOrFail();
        $medKg = Medida::where('simbolo', 'kg')->firstOrFail();
        $medL  = Medida::where('simbolo', 'L')->firstOrFail();

        // Sucursal y Área por defecto para items
        $sucursal = Sucursal::first() ?? Sucursal::create([
            'descripcion' => 'Casa Matriz',
            'estado'      => 'Activo',
        ]);

        $areaPint = Area::firstOrCreate(
            ['descripcion' => 'Área Pinturas'],
            [
                'estado'      => 'Activo',
                'sucursal_id' => $sucursal->id,
            ]
        );

        $areaHerr = Area::firstOrCreate(
            ['descripcion' => 'Depósito Herramientas'],
            [
                'estado'      => 'Activo',
                'sucursal_id' => $sucursal->id,
            ]
        );

        $areaMat = Area::firstOrCreate(
            ['descripcion' => 'Depósito Materiales'],
            [
                'estado'      => 'Activo',
            'sucursal_id' => $sucursal->id,
            ]
        );

        // Herramientas
        $herrs = [
            ['Taladro percutor',    'HERR-001', $catHerr, $medU,  $areaHerr],
            ['Amoladora 4.5"',      'HERR-002', $catHerr, $medU,  $areaHerr],
            ['Llave ajustable',     'HERR-003', $catHerr, $medU,  $areaHerr],
            ['Multímetro',          'HERR-004', $catElec, $medU,  $areaHerr],
            ['Prensa sargento',     'HERR-005', $catHerr, $medU,  $areaHerr],
            ['Cortatubos',          'HERR-006', $catPlom, $medU,  $areaHerr],
        ];

        foreach ($herrs as [$desc, $cod, $cat, $med, $area]) {
            Item::updateOrCreate(
                ['codigo' => $cod],
                [
                    'categoria_id'       => $cat->id,
                    'medida_id'          => $med->id,
                    'area_id'            => $area->id,
                    'descripcion'        => $desc,
                    'fabricante'         => fake()->randomElement(['Bosch', 'Makita', 'Stanley', 'Truper']),
                    'cantidad'           => fake()->numberBetween(4, 12),
                    'piezas'             => 0,
                    'costo_unitario'     => fake()->randomFloat(2, 50, 400),
                    'estado'             => 'Disponible',
                    'tipo'               => 'Herramienta',
                    'ubicacion'          => 'Almacén A',
                    'fecha_registro'     => now(),
                ]
            );
        }

        // Materiales
        $mats = [
            ['Guantes de nitrilo (par)', 'MAT-001', $catMat,  $medU,  $areaMat],
            ['Discos de corte 4.5"',     'MAT-002', $catMat,  $medU,  $areaMat],
            ['Cinta aislante',           'MAT-003', $catElec, $medU,  $areaMat],
            ['Cable THHN 12 AWG',        'MAT-004', $catElec, $medM,  $areaMat],
            ['Tubo PVC 1/2"',            'MAT-005', $catPlom, $medM,  $areaMat],
            ['Pintura látex blanca',     'MAT-006', $catPint, $medL,  $areaPint],
            ['Electrodos 6013 (kg)',     'MAT-007', $catMat,  $medKg, $areaMat],
            ['Tornillos 1" (u)',         'MAT-008', $catMat,  $medU,  $areaMat],
        ];

        foreach ($mats as [$desc, $cod, $cat, $med, $area]) {
            Item::updateOrCreate(
                ['codigo' => $cod],
                [
                    'categoria_id'       => $cat->id,
                    'medida_id'          => $med->id,
                    'area_id'            => $area->id,
                    'descripcion'        => $desc,
                    'fabricante'         => fake()->randomElement(['Genérico', 'AcerosX', 'PlastiCo', 'Pintulac']),
                    'cantidad'           => fake()->numberBetween(30, 250),
                    'piezas'             => 0,
                    'costo_unitario'     => fake()->randomFloat(2, 1, 50),
                    'estado'             => 'Disponible',
                    'tipo'               => 'Material',
                    'ubicacion'          => 'Almacén B',
                    'fecha_registro'     => now(),
                    'imagen_path'        => null,
                    'imagen_thumb'       => null,
                ]
            );
        }
    }

    private function seedPrestamosConMovimientos(): void
    {
        $this->command->warn('  - Préstamos/Movimientos/Devoluciones…');

        $personas  = Persona::inRandomOrder()->take(6)->get();
        $proyectos = Proyecto::inRandomOrder()->take(4)->get();
        $items     = Item::orderBy('codigo')->get();

        if ($items->isEmpty()) {
            $this->command->warn('    ! No hay items, saltando préstamos…');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $codigo = 'PRE-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT);

            $tipo     = fake()->randomElement(['Persona', 'Proyecto']);
            $persona  = $tipo === 'Persona' ? $personas->random() : null;
            $proyecto = $tipo === 'Proyecto' ? $proyectos->random() : null;

            DB::transaction(function () use ($codigo, $tipo, $persona, $proyecto, $items) {
                $prestamo = Prestamo::create([
                    'codigo'       => $codigo,
                    'fecha'        => fake()->dateTimeBetween('-20 days', '-1 day')->format('Y-m-d'),
                    'estado'       => 'Activo',
                    'tipo_destino' => $tipo,
                    'persona_id'   => $persona?->id,
                    'proyecto_id'  => $proyecto?->id,
                    'user_id'      => User::first()->id,
                    'nota'         => fake()->sentence(8),
                ]);

                // 2 a 5 líneas por préstamo
                $lineas = $items->shuffle()->take(fake()->numberBetween(2, 5));

                foreach ($lineas as $it) {
                    // Refrescar stock real ANTES de decidir cantidad
                    $it->refresh();
                    $available = (int) $it->cantidad;

                    if ($available <= 0) {
                        continue;
                    }

                    $cant = fake()->numberBetween(1, min(8, $available));
                    if ($cant <= 0) {
                        continue;
                    }

                    DetallePrestamo::create([
                        'prestamo_id'       => $prestamo->id,
                        'item_id'           => $it->id,
                        'cantidad_prestada' => $cant,
                        'cantidad_devuelta' => 0,
                        'costo_unitario'    => $it->costo_unitario,
                        'subtotal'          => $it->costo_unitario * $cant,
                    ]);

                    // Descontar stock (nunca bajará de 0 porque cant <= available)
                    $it->decrement('cantidad', $cant);

                    Movimiento::create([
                        'item_id'     => $it->id,
                        'accion'     => "lo qeu sea",
                        'tipo'        => 'Egreso',
                        'cantidad'    => $cant,
                        'fecha'       => now()->subDays(fake()->numberBetween(1, 20)),
                        'user_id'     => User::first()->id,
                        'prestamo_id' => $prestamo->id,
                        'nota'        => 'Préstamo ' . $prestamo->codigo,
                    ]);
                }

                // 75%: registrar devolución (parcial o completa)
                if (fake()->boolean(75)) {
                    $this->registrarDevolucionAleatoria($prestamo);
                }
            });
        }
    }

    private function registrarDevolucionAleatoria(Prestamo $prestamo): void
    {
        $prestamo->load('detalles.item');

        $dev = Devolucion::create([
            'prestamo_id' => $prestamo->id,
            'estado'      => 'Pendiente',
            'fecha'       => now()->subDays(fake()->numberBetween(0, 5)),
            'user_id'     => User::inRandomOrder()->first()->id,
            'nota'        => fake()->boolean() ? fake()->sentence(6) : null,
        ]);

        foreach ($prestamo->detalles as $d) {
            $item = $d->item;

            if (!$item->permite_devolucion) {
                // consumible puro: no devuelve
                continue;
            }

            $pendiente = $d->cantidad_prestada - $d->cantidad_devuelta;
            if ($pendiente <= 0) {
                continue;
            }

            // parcial (50%) o total
            $aDevolver = fake()->boolean(50)
                ? fake()->numberBetween(1, $pendiente)
                : $pendiente;

            DetalleDevolucion::create([
                'devolucion_id' => $dev->id,
                'item_id'       => $item->id,
                'cantidad'      => $aDevolver,
            ]);

            // actualizar detalle préstamo
            $d->increment('cantidad_devuelta', $aDevolver);

            // devolver a stock
            $item->increment('cantidad', $aDevolver);

            Movimiento::create([
                'item_id'       => $item->id,
                'tipo'          => 'Ingreso',
                'cantidad'      => $aDevolver,
                'fecha'         => now(),
                'user_id'       => $dev->user_id,
                'prestamo_id'   => $prestamo->id,
                'devolucion_id' => $dev->id,
                'nota'          => 'Devolución ' . $prestamo->codigo,
            ]);
        }

        // Estado del préstamo y de la devolución
        $prestamo->refresh()->load('detalles.item');

        $completo = $prestamo->detalles->every(function ($d) {
            return $d->cantidad_devuelta >= $d->cantidad_prestada || !$d->item->permite_devolucion;
        });

        $prestamo->update(['estado' => $completo ? 'Completo' : 'Activo']);
        $dev->update(['estado' => $completo ? 'Completa' : 'Parcial']);
    }

    /* ====================== HELPERS ====================== */

    private function fakePhone(): string
    {
        return (string) fake()->numberBetween(70000001, 79999999);
    }
}
