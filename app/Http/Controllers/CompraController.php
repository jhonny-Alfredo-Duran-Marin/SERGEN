<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $tipo = $request->string('tipo')->toString();
        $estado = $request->string('estado')->toString();
        $fecha = $request->date('fecha');

        $query = Compra::query()->with('user:id,name');

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

        $filtered = $query->clone()->toBase();
        $total = $filtered->count();
        $pendientes = $filtered->clone()->where('estado_procesamiento', 'Pendiente')->count();
        $resueltos = $filtered->clone()->where('estado_procesamiento', 'Resuelto')->count();
        $totalGastado = $filtered->clone()->sum('costo_total');

        $compras = $query->orderBy('fecha_compra', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('compras.index', compact('compras', 'total', 'pendientes', 'resueltos', 'totalGastado'));
    }

    public function create()
    {
        return view('compras.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_compra' => ['required', 'date'],
            'descripcion'  => ['required', 'string', 'max:255'],
            'costo_total'  => ['required', 'numeric', 'min:0'],
            'cantidad'     => ['required', 'integer', 'min:1'],
            'tipo_compra'  => ['required', Rule::in(['Herramienta', 'Material', 'Insumos', 'Otros'])],
            'imagen'       => ['nullable', 'image', 'max:2048'],
        ]);

        // Estado automático
        $data['estado_procesamiento'] = in_array($data['tipo_compra'], ['Herramienta', 'Material'])
            ? 'Pendiente'
            : 'Resuelto';

        $data['user_id'] = auth()->id();

        // Subir imagen
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('compras', 'public');
        }

        $compra = Compra::create($data);

        // Registrar movimiento
        Movimiento::create([
            'item_id' => null,
            'accion' => 'CREAR_COMPRA',
            'cantidad' => $compra->cantidad,
            'fecha' => now(),
            'user_id' => auth()->id(),
            'nota' => 'Compra registrada: ' . $compra->descripcion,
        ]);

        return redirect()->route('compras.index')->with('status', 'Compra registrada.');
    }

    public function edit(Compra $compra)
    {
        return view('compras.edit', compact('compra'));
    }

    public function update(Request $request, Compra $compra)
    {
        $data = $request->validate([
            'fecha_compra' => ['required', 'date'],
            'descripcion'  => ['required', 'string', 'max:255'],
            'costo_total'  => ['required', 'numeric', 'min:0'],
            'cantidad'     => ['required', 'integer', 'min:1'],
            'tipo_compra'  => ['required', Rule::in(['Herramienta', 'Material', 'Insumos', 'Otros'])],
            'estado_procesamiento' => ['required', Rule::in(['Pendiente', 'Resuelto'])],
            'imagen'       => ['nullable', 'image', 'max:2048'],
        ]);

        // Si hay imagen nueva
        if ($request->hasFile('imagen')) {
            if ($compra->imagen) {
                Storage::disk('public')->delete($compra->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('compras', 'public');
        }

        $compra->update($data);

        Movimiento::create([
            'item_id' => null,
            'accion' => 'EDITAR_COMPRA',
            'cantidad' => $compra->cantidad,
            'fecha' => now(),
            'user_id' => auth()->id(),
            'nota' => 'Compra actualizada: ' . $compra->descripcion,
        ]);

        return redirect()->route('compras.index')->with('status', 'Compra actualizada.');
    }
    public function show(Compra $compra)
    {
        return view('compras.show', compact('compra'));
    }


    public function destroy(Compra $compra)
    {
        if ($compra->imagen) {
            Storage::disk('public')->delete($compra->imagen);
        }

        $id = $compra->id;
        $desc = $compra->descripcion;

        $compra->delete();

        Movimiento::create([
            'item_id' => null,
            'accion' => 'ELIMINAR_COMPRA',
            'cantidad' => null,
            'fecha' => now(),
            'user_id' => auth()->id(),
            'nota' => "Compra eliminada: $desc",
        ]);

        return redirect()->route('compras.index')->with('status', 'Compra eliminada.');
    }
}
