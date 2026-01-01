<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ConsumoController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\DotacionController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemSearchController;
use App\Http\Controllers\KitEmergenciaController;
use App\Http\Controllers\MedidaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ProyectosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Auth;
// web.php
Route::get('/', fn() => view('welcome'));
Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    // PERMISOS
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')->middleware('permission:permissions.view');
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->name('permissions.create')->middleware('permission:permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])
        ->name('permissions.store')->middleware('permission:permissions.create');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->name('permissions.edit')->middleware('permission:permissions.update');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])
        ->name('permissions.update')->middleware('permission:permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')->middleware('permission:permissions.delete');

    // ROLES
    Route::get('/roles', [RoleController::class, 'index'])
        ->name('roles.index')->middleware('permission:roles.view');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->name('roles.create')->middleware('permission:roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')->middleware('permission:roles.update');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')->middleware('permission:roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')->middleware('permission:roles.delete');

    // USUARIOS ↔ ROLES
    Route::get('/users', [UserRoleController::class, 'index'])
        ->name('users.index')->middleware('permission:users.view');
    Route::get('/users/{user}/roles', [UserRoleController::class, 'edit'])
        ->name('users.roles.edit')->middleware('permission:users.assign.roles');
    Route::put('/users/{user}/roles', [UserRoleController::class, 'update'])
        ->name('users.roles.update')->middleware('permission:users.assign.roles');

    // PERSONAS
    Route::get('personas', [PersonaController::class, 'index'])
        ->name('personas.index')->middleware('permission:personas.view');
    Route::get('personas/create', [PersonaController::class, 'create'])
        ->name('personas.create')->middleware('permission:personas.create');
    Route::post('personas', [PersonaController::class, 'store'])
        ->name('personas.store')->middleware('permission:personas.create');
    Route::get('personas/{persona}/edit', [PersonaController::class, 'edit'])
        ->name('personas.edit')->middleware('permission:personas.update');
    Route::put('personas/{persona}', [PersonaController::class, 'update'])
        ->name('personas.update')->middleware('permission:personas.update');
    Route::delete('personas/{persona}', [PersonaController::class, 'destroy'])
        ->name('personas.destroy')->middleware('permission:personas.delete');
    Route::get('personas/{persona}', [PersonaController::class, 'show'])
        ->name('personas.show')->middleware('permission:personas.view');

    // PROYECTOS / CATEGORÍAS / MEDIDAS / ITEMS (estos ya tienen permisos en sus controllers)
    Route::resource('proyectos', ProyectosController::class)->names('proyectos');
    Route::resource('areas', AreaController::class)->names('areas');
    Route::resource('categorias', controller: CategoriaController::class)->names('categorias');
    Route::resource('medidas',   MedidaController::class)->names('medidas');
    Route::resource('items',     ItemController::class)->names('items');
    Route::resource('sucursal', SucursalController::class)->names('sucursal');

    // PRÉSTAMOS
    Route::resource('prestamos', PrestamoController::class)->names('prestamos');
    Route::get(
        '/prestamos/{prestamo}/imprimir',
        [PrestamoController::class, 'ImpresionPrestamo']
    )->name('prestamos.imprimir');


    // DEVOLUCIONES (anidadas)
    Route::get('prestamos/{prestamo}/devoluciones/create', [DevolucionController::class, 'create'])
        ->name('devoluciones.create')
        ->middleware('permission:devoluciones.create');
    Route::post('prestamos/{prestamo}/devoluciones', [DevolucionController::class, 'store'])
        ->name('devoluciones.store')
        ->middleware('permission:devoluciones.create');

    // DOTACIONES
    Route::get('/dotaciones', [DotacionController::class, 'index'])->name('dotaciones.index');
    Route::get('/dotaciones/create', [DotacionController::class, 'create'])->name('dotaciones.create');
    Route::post('/dotaciones', [DotacionController::class, 'store'])->name('dotaciones.store');

    Route::get('/dotaciones/{dotacion}', [DotacionController::class, 'show'])->name('dotaciones.show');
    Route::get('/dotaciones/{dotacion}/edit', [DotacionController::class, 'edit'])->name('dotaciones.edit');
    Route::put('/dotaciones/{dotacion}', [DotacionController::class, 'update'])->name('dotaciones.update');
    Route::delete('/dotaciones/{dotacion}', [DotacionController::class, 'destroy'])->name('dotaciones.destroy');

    Route::get('/dotaciones/{dotacion}/devolver', [DotacionController::class, 'formDevolver'])->name('dotaciones.devolver.form');
    Route::post('/dotaciones/{dotacion}/devolver', [DotacionController::class, 'procesarDevolucion'])->name('dotaciones.devolver.store');
    Route::get('dotaciones/{dotacion}/recibo', [DotacionController::class, 'imprimirRecibo'])->name('dotaciones.recibo');


    Route::resource('kits', KitEmergenciaController::class)
        ->names('kits');
    Route::get('api/items/search', [ItemSearchController::class, 'search'])
        ->name('items.search');
    Route::get(
        'prestamos/{prestamo}/imprimir-historial',
        [PrestamoController::class, 'ImpresionHistorial']
    )->name('prestamos.imprimir.historial');

    Route::resource('compras', CompraController::class);
    Route::patch('compras/{compra}/resolver', [CompraController::class, 'resolver'])->name('compras.resolver');

    Route::get('/movimientos', [MovimientoController::class, 'index'])
        ->name('movimientos.index')
        ->middleware('auth');

    Route::post('/compras/solicitar', [CompraController::class, 'solicitar'])
        ->name('compras.solicitar');



    Route::resource('incidentes', IncidenteController::class);

    // registrar devolución
    Route::get('incidentes/{incidente}/devolver', [IncidenteController::class, 'devolverForm'])
        ->name('incidentes.devolver');

    Route::post('incidentes/{incidente}/devolver', [IncidenteController::class, 'registrarDevolucion'])
        ->name('incidentes.devolver.store');

    // completar incidente
    Route::post('incidentes/{incidente}/completar', [IncidenteController::class, 'completar'])
        ->name('incidentes.completar');

    Route::get(
        'incidentes/devolucion/{devolucion}/recibo',
        [IncidenteController::class, 'recibo']
    )
        ->name('incidentes.recibo');


    Route::resource('prestamos', PrestamoController::class);

    // Rutas de devoluciones
    Route::get('prestamos/{prestamo}/devoluciones', [DevolucionController::class, 'index'])
        ->name('devoluciones.index');
    Route::get('prestamos/{prestamo}/devoluciones/create', [DevolucionController::class, 'create'])
        ->name('devoluciones.create');
    Route::post('prestamos/{prestamo}/devoluciones', [DevolucionController::class, 'store'])
        ->name('devoluciones.store');

    // Impresión de devoluciones
    Route::get('devoluciones/{devolucion}/recibo', [DevolucionController::class, 'imprimirRecibo'])
        ->name('devoluciones.imprimir.recibo');
    Route::get('prestamos/{prestamo}/devoluciones/historial-pdf', [DevolucionController::class, 'imprimirHistorial'])
        ->name('devoluciones.imprimir.historial');
    Route::post('devoluciones/{devolucion}/anular', [DevolucionController::class, 'anular'])->name('devoluciones.anular');

    Route::get('consumos', [ConsumoController::class, 'index'])->name('consumos.index');
    Route::get('consumos/pdf', [ConsumoController::class, 'reportepdf'])->name('consumos.pdf'); // Reporte de todo el proyecto
    Route::get('consumos/{consumo}/recibo', [ConsumoController::class, 'imprimirRecibo'])->name('consumos.recibo'); // Recibo de una sola fila
    Route::get('consumos/{consumo}', [ConsumoController::class, 'show'])->name('consumos.show');
});
