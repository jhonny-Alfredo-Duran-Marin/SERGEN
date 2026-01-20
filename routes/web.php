<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    AreaController, CategoriaController, CompraController, ConsumoController,
    DevolucionController, DotacionController, IncidenteController, ItemController,
    ItemSearchController, KitEmergenciaController, MedidaController, MovimientoController,
    ProyectosController, PermissionController, PersonaController, PrestamoController,
    RoleController, SucursalController, UbicacionController, UserRoleController, HomeController
};

// Rutas Públicas
Route::get('/', fn() => view('welcome'));
Auth::routes(['register' => false]);

// Rutas Protegidas por Autenticación
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // SEGURIDAD (Roles, Permisos y Usuarios)
    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/roles', [UserRoleController::class, 'edit'])->name('users.roles.edit');
    Route::put('/users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');

    // MÓDULOS DE INVENTARIO Y GESTIÓN (Usando Resource)
    Route::resource('personas', PersonaController::class);
    Route::resource('proyectos', ProyectosController::class);
    Route::resource('areas', AreaController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('medidas', MedidaController::class);
    Route::resource('items', ItemController::class);
    Route::resource('sucursal', SucursalController::class);
    Route::resource('kits', KitEmergenciaController::class);
    Route::resource('compras', CompraController::class);
    Route::resource('incidentes', IncidenteController::class);
    Route::resource('prestamos', PrestamoController::class);
    Route::resource('ubicaciones', UbicacionController::class)->parameters(['ubicaciones' => 'ubicacion']);

    // MÓDULOS CON RUTAS MANUALES O ADICIONALES

    // Dotaciones
    Route::resource('dotaciones', DotacionController::class);
    Route::get('/dotaciones/{dotacion}/devolver', [DotacionController::class, 'formDevolver'])->name('dotaciones.devolver.form');
    Route::post('/dotaciones/{dotacion}/devolver', [DotacionController::class, 'procesarDevolucion'])->name('dotaciones.devolver.store');
    Route::get('dotaciones/{dotacion}/recibo', [DotacionController::class, 'imprimirRecibo'])->name('dotaciones.recibo');

    // Devoluciones (Anidadas a Préstamos e Incidentes)
    Route::get('prestamos/{prestamo}/devoluciones', [DevolucionController::class, 'index'])->name('devoluciones.index');
    Route::get('prestamos/{prestamo}/devoluciones/create', [DevolucionController::class, 'create'])->name('devoluciones.create');
    Route::post('prestamos/{prestamo}/devoluciones', [DevolucionController::class, 'store'])->name('devoluciones.store');
    Route::get('devoluciones/{devolucion}/recibo', [DevolucionController::class, 'imprimirRecibo'])->name('devoluciones.imprimir.recibo');
    Route::get('prestamos/{prestamo}/devoluciones/historial-pdf', [DevolucionController::class, 'imprimirHistorial'])->name('devoluciones.imprimir.historial');
    Route::post('devoluciones/{devolucion}/anular', [DevolucionController::class, 'anular'])->name('devoluciones.anular');

    // Compras / Movimientos / Consumos
    Route::patch('compras/{compra}/resolver', [CompraController::class, 'resolver'])->name('compras.resolver');
    Route::post('/compras/solicitar', [CompraController::class, 'solicitar'])->name('compras.solicitar');
    Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
    Route::get('consumos', [ConsumoController::class, 'index'])->name('consumos.index');
    Route::get('consumos/pdf', [ConsumoController::class, 'reportepdf'])->name('consumos.pdf');
    Route::get('consumos/{consumo}/recibo', [ConsumoController::class, 'imprimirRecibo'])->name('consumos.recibo');
    Route::get('consumos/{consumo}', [ConsumoController::class, 'show'])->name('consumos.show');

    // Incidentes Extras
    Route::get('incidentes/{incidente}/devolver', [IncidenteController::class, 'devolverForm'])->name('incidentes.devolver');
    Route::post('incidentes/{incidente}/devolver', [IncidenteController::class, 'registrarDevolucion'])->name('incidentes.devolver.store');
    Route::post('incidentes/{incidente}/completar', [IncidenteController::class, 'completar'])->name('incidentes.completar');
    Route::get('incidentes/devolucion/{devolucion}/recibo', [IncidenteController::class, 'recibo'])->name('incidentes.recibo');

    // APIs y Búsquedas
    Route::get('api/areas/{area}/ubicaciones', [ItemController::class, 'getUbicacionesPorArea']);
    Route::get('api/items/search', [ItemSearchController::class, 'search'])->name('items.search');

    // Impresiones Específicas
    Route::get('/prestamos/{prestamo}/imprimir', [PrestamoController::class, 'ImpresionPrestamo'])->name('prestamos.imprimir');
    Route::get('prestamos/{prestamo}/imprimir-historial', [PrestamoController::class, 'ImpresionHistorial'])->name('prestamos.imprimir.historial');
});
