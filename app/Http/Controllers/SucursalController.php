<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SucursalController extends Controller
{
    public function __construct()
    {

        $this->middleware(['permission:sucursal.view'])->only(['index', 'show']);
        $this->middleware(['permission:sucursal.create'])->only(['create', 'store']);
        $this->middleware(['permission:sucursal.update'])->only(['edit', 'update']);
        $this->middleware(['permission:sucursal.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q      = trim((string) $request->input('q'));
        $estado = $request->input('estado'); // 'Activo' | 'Pasivo' | null

        $base = Sucursal::query()
            ->when($q, function ($qb) use ($q) {
                $qb->where('descripcion', 'like', "%{$q}%");
            })
            ->when($estado, function ($qb) use ($estado) {
                $qb->where('estado', $estado);
            });

        // Contadores (respetando el filtro de búsqueda por texto)
        $total   = (clone $base)->count();
        $activos = (clone $base)->where('estado', 'Activo')->count();
        $pasivos = $total - $activos;

        $sucursales = (clone $base)
            ->orderBy('descripcion')
            ->paginate(20)
            ->withQueryString();

        return view('sucursal.index', compact('sucursales', 'total', 'activos', 'pasivos', 'q', 'estado'));
    }

    public function create()
    {
        return view('sucursal.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => [
                'required',
                'string',
                'max:150',
                Rule::unique(Sucursal::class, 'descripcion'),
            ],
            'estado' => ['required', Rule::in(['Activo', 'Pasivo'])],
        ]);

        Sucursal::create($data);

        return redirect()
            ->route('sucursal.index')
            ->with('status', 'Sucursal creada correctamente.');
    }

    public function show(Sucursal $sucursal)
    {
        return view('sucursal.show', compact('sucursal'));
    }

    public function edit(Sucursal $sucursal)
    {
        return view('sucursal.edit', compact('sucursal'));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $data = $request->validate([
            'descripcion' => [
                'required',
                'string',
                'max:150',
                Rule::unique(Sucursal::class, 'descripcion')->ignore($sucursal->id),
            ],
            'estado' => ['required', Rule::in(['Activo', 'Pasivo'])],
        ]);

        $sucursal->update($data);

        return redirect()
            ->route('sucursal.index')
            ->with('status', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();

        return redirect()
            ->route('sucursal.index')
            ->with('status', 'Sucursal eliminada correctamente.');
    }
}
