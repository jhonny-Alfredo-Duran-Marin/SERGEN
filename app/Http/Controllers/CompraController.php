<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // Para las estadísticas

class CompraController extends Controller
{
    /**
     * Muestra la lista de compras con filtros y estadísticas.
     */
    public function index(Request $request)
    {
        // Validación de filtros (usando 'string' y 'date' para limpiar)
        $q = $request->string('q')->toString();
        $tipo = $request->string('tipo')->toString();
        $estado = $request->string('estado')->toString();
        $fecha = $request->date('fecha');

        $query = Compra::query()->with('user:id,name'); // Carga el usuario

        // Aplicar filtros
        if ($q !== '') {
            $query->where('descripcion', 'like', "%{$q}%");
        }
        if (in_array($tipo, ['Herramienta', 'Material', 'Insumos', 'Otros'])) {
            $query->where('tipo_compra', $tipo);
        }
        if (in_array($estado, ['Pendiente', 'Resuelto'])) {
            $query->where('estado_procesamiento', $estado);
        }
        if ($fecha) {
            $query->whereDate('fecha_compra', $fecha);
        }

        // --- Estadísticas (igual que en tu vista de Items) ---
        $statsQuery = DB::table('compras'); // Usamos DB::table para eficiencia

        // Clonar la consulta filtrada si existen filtros, sino usar la base
        $filteredQuery = $query->clone()->toBase();

        $total = $filteredQuery->count();
        $pendientes = $filteredQuery->clone()->where('estado_procesamiento', 'Pendiente')->count();
        $resueltos = $filteredQuery->clone()->where('estado_procesamiento', 'Resuelto')->count();
        $totalGastado = $filteredQuery->clone()->sum('costo_total');

        // Paginación
        $compras = $query->orderBy('fecha_compra', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('compras.index', compact(
            'compras',
            'total',
            'pendientes',
            'resueltos',
            'totalGastado'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva compra.
     */
    public function create()
    {
        return view('compras.create');
    }

    /**
     * Guarda la nueva compra en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_compra' => ['required', 'date'],
            'descripcion' => ['required', 'string', 'max:255'],
            'costo_total' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'tipo_compra' => ['required', Rule::in(['Herramienta', 'Material', 'Insumos', 'Otros'])],
        ]);

        // --- Lógica de Alerta Automática ---
        if (in_array($data['tipo_compra'], ['Herramienta', 'Material'])) {
            // ¡ALERTA! Esto debe ser revisado para inventario
            $data['estado_procesamiento'] = 'Pendiente';
        } else {
            // Es un gasto (Insumo, Otros). No necesita alerta.
            $data['estado_procesamiento'] = 'Resuelto'; // O 'Gasto' si cambias tu enum
        }

        $data['user_id'] = auth()->id();

        Compra::create($data);

        // Generamos un mensaje de status diferente si es una alerta
        $status = 'Compra registrada.';
        if ($data['estado_procesamiento'] === 'Pendiente') {
            $status = '¡Compra registrada! Se generó una alerta pendiente de revisión.';
        }

        return redirect()->route('compras.index')->with('status', $status);
    }
    public function solicitar(Request $request)
    {
        // Guardar en tabla solicitudes_compras o enviar email
        session()->flash('status', 'Solicitud enviada al administrador!');
        return redirect()->back();
    }

    /**
     * Muestra el formulario para editar una compra.
     * (Asumimos que solo editamos, no "procesamos" aquí)
     */
    public function edit(Compra $compra)
    {
        return view('compras.edit', compact('compra'));
    }

    /**
     * Actualiza la compra en la base de datos.
     */
    public function update(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'fecha_compra' => ['required', 'date'],
            'descripcion' => ['required', 'string', 'max:255'],
            'costo_total' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'tipo_compra' => ['required', Rule::in(['Herramienta', 'Material', 'Insumos', 'Otros'])],
            'estado_procesamiento' => ['required', Rule::in(['Pendiente', 'Resuelto'])], // Permitimos cambiar estado
        ]);

        $compra->update($data);

        return redirect()->route('compras.index')->with('status', 'Compra actualizada.');
    }

    /**
     * Elimina una compra de la base de datos.
     */
    public function destroy(Compra $compra)
    {
        $compra->delete();
        return redirect()->route('compras.index')->with('status', 'Compra eliminada.');
    }

    /**
     * (Opcional) Un método rápido para marcar como resuelto desde el index.
     * Necesitarás una ruta para esto:
     * Route::patch('compras/{compra}/resolver', [CompraController::class, 'resolver'])->name('compras.resolver');
     */
    public function resolver(Compra $compra)
    {
        if ($compra->estado_procesamiento === 'Pendiente') {
            $compra->update(['estado_procesamiento' => 'Resuelto']);
            return redirect()->route('compras.index')->with('status', 'Compra marcada como resuelta.');
        }
        return redirect()->route('compras.index');
    }
}
