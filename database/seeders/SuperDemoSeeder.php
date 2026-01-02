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
use App\Models\Ubicacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('> SuperDemoSeeder iniciando…');

        // 1) Roles & Permisos
        if (class_exists(RolePermissionSeeder::class)) {
            $this->call(RolePermissionSeeder::class);
        }

        // 2) Entidades base
        $this->seedUsuariosPersonas();
        $this->seedSucursales();

        // 3) Catálogos y Ubicaciones (Orden importante: Sucursal -> Area -> Ubicacion)
        $this->seedCatalogosYAreas();
        $this->seedUbicaciones();

        // 4) Operaciones
        $this->seedProyectos();
        $this->seedItems();

        $this->command->info('> SuperDemoSeeder listo ✅');
    }

    /* ====================== BLOQUES DE SEEDING ====================== */

    private function seedUsuariosPersonas(): void
    {
        $this->command->warn('  - Personas/Usuarios…');

        $saName  = env('SUPERADMIN_NAME', 'Super Admin');
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
                'password'          => $saPass,
                'persona_id'        => $saPersona->id,
                'email_verified_at' => now(),
            ]
        );

        if (method_exists($super, 'syncRoles')) {
            $super->syncRoles(['SuperAdmin']);
        }

        // Personal adicional
        $admins = [
            ['Ana Romero', 'ana.romero@example.com', 'Gerente de Obra'],
            ['Luis Gutiérrez', 'luis.gtz@example.com', 'Jefe de Almacén'],
        ];

        foreach ($admins as [$n, $e, $cargo]) {
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
    }

    private function seedSucursales(): void
    {
        $this->command->warn('  - Sucursales…');
        $data = [
            ['descripcion' => 'Casa Matriz', 'estado' => 'Activo'],
            ['descripcion' => 'Sucursal Norte', 'estado' => 'Activo'],
            ['descripcion' => 'Sucursal Sur', 'estado' => 'Activo'],
        ];
        foreach ($data as $s) {
            Sucursal::firstOrCreate(['descripcion' => $s['descripcion']], ['estado' => $s['estado']]);
        }
    }

    private function seedCatalogosYAreas(): void
    {
        $this->command->warn('  - Catálogos y Áreas…');

        foreach (['Herramientas', 'Materiales', 'EPP', 'Electricidad'] as $c) {
            Categoria::firstOrCreate(['descripcion' => $c], ['estado' => 'Activo']);
        }

        $meds = [['Unidad', 'u'], ['Metro', 'm'], ['Kilogramo', 'kg'], ['Litro', 'L']];
        foreach ($meds as [$desc, $sim]) {
            Medida::firstOrCreate(['simbolo' => $sim], ['descripcion' => $desc]);
        }

        $sucursales = Sucursal::all();
        $areas = ['Depósito Herramientas', 'Depósito Materiales', 'Área Pinturas', 'Almacén Central'];
        foreach ($areas as $a) {
            Area::firstOrCreate(
                ['descripcion' => $a],
                ['estado' => 'Activo', 'sucursal_id' => $sucursales->random()->id]
            );
        }
    }

    private function seedUbicaciones(): void
    {
        $this->command->warn('  - Generando Ubicaciones por Área…');
        $areas = Area::all();
        foreach ($areas as $area) {
            for ($i = 1; $i <= 3; $i++) {
                Ubicacion::firstOrCreate([
                    'descripcion' => "Estante " . str_pad($i, 2, '0', STR_PAD_LEFT) . " (" . $area->descripcion . ")",
                    'area_id' => $area->id
                ], ['estado' => 'Activo']);
            }
        }
    }

    private function seedProyectos(): void
    {
        $this->command->warn('  - Proyectos…');
        for ($i = 1; $i <= 3; $i++) {
            Proyecto::firstOrCreate(
                ['codigo' => "PRJ-00$i"],
                [
                    'descripcion' => fake()->sentence(3),
                    'empresa' => fake()->company(),
                    'estado' => 'Abierto',
                    'fecha_inicio' => now()->subMonths(2)
                ]
            );
        }
    }

    private function seedItems(): void
    {
        $this->command->warn('  - Items con stock y ubicación relacional…');

        $catHerr = Categoria::where('descripcion', 'Herramientas')->first();
        $catMat  = Categoria::where('descripcion', 'Materiales')->first();
        $medU    = Medida::where('simbolo', 'u')->first();
        $areaH   = Area::where('descripcion', 'Depósito Herramientas')->first();
        $areaM   = Area::where('descripcion', 'Depósito Materiales')->first();

        // Definición de items demo
        $itemsDemo = [
            ['Taladro Percutor', 'ITEM-001', $catHerr, $medU, $areaH, 'Herramienta', 150.00],
            ['Amoladora 4.5"',   'ITEM-002', $catHerr, $medU, $areaH, 'Herramienta', 120.00],
            ['Casco Seguridad',  'ITEM-003', $catMat,  $medU, $areaM, 'Material',    25.00],
            ['Guantes Nitrilo',  'ITEM-004', $catMat,  $medU, $areaM, 'Material',    5.00],
        ];

        foreach ($itemsDemo as [$desc, $cod, $cat, $med, $area, $tipo, $costo]) {
            // Buscamos una ubicación que pertenezca al área del item
            $ubi = Ubicacion::where('area_id', $area->id)->inRandomOrder()->first();

            Item::updateOrCreate(
                ['codigo' => $cod],
                [
                    'categoria_id'   => $cat->id,
                    'medida_id'      => $med->id,
                    'area_id'        => $area->id,
                    'ubicacion_id'   => $ubi->id, // RELACIÓN DINÁMICA
                    'descripcion'    => $desc,
                    'fabricante'     => fake()->randomElement(['Bosch', 'Stanley', 'Truper']),
                    'cantidad'       => fake()->numberBetween(10, 50),
                    'costo_unitario' => $costo,
                    'estado'         => 'Disponible',
                    'tipo'           => $tipo,
                    'fecha_registro' => now(),
                ]
            );
        }
    }

    private function fakePhone(): string
    {
        return (string) fake()->numberBetween(70000000, 79999999);
    }
}
