<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Sucursal; // AÑADIR ESTA LÍNEA
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
     public function __construct()
    {
        // Descomenta si tienes seed-eados estos permisos
        $this->middleware(['permission:areas.view'])->only(['index', 'show']);
        $this->middleware(['permission:areas.create'])->only(['create', 'store']);
        $this->middleware(['permission:areas.update'])->only(['edit', 'update']);
        $this->middleware(['permission:areas.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q = trim($request->input('q'));
        $estado = $request->input('estado');

        $base = Area::query()
            ->with('sucursal')
            ->when($q, fn($qb) => $qb->where('descripcion', 'like', "%{$q}%"))
            ->when($estado, fn($qb) => $qb->where('estado', $estado));

        $total   = (clone $base)->count();
        $activos = (clone $base)->where('estado', 'Activo')->count();
        $pasivos = $total - $activos;

        $areas = (clone $base)
            ->orderBy('descripcion')
            ->paginate(20)
            ->withQueryString();

        return view("areas.index", compact('areas', 'total', 'activos', 'pasivos'));
    }


    public function create()
    {
        // Obtener la lista de sucursales para el <select>
        $sucursales = Sucursal::orderBy('descripcion')->get();
        return view('areas.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => ['required', 'string', 'max:150', 'unique:areas,descripcion'],
            'estado'      => ['required', Rule::in(['Activo', 'Pasivo'])],
            // AÑADIR VALIDACIÓN PARA sucursal_id
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
        ]);
        Area::create($data);
        return redirect()->route('areas.index')->with('status', 'Area creada.');
    }

    public function edit(Area $area)
    {
        // Obtener la lista de sucursales para el <select>
        $sucursales = Sucursal::orderBy('descripcion')->get();
        return view('areas.edit', compact('area', 'sucursales'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'descripcion' => ['required', 'string', 'max:150', Rule::unique('areas', 'descripcion')->ignore($area->id)],
            'estado'      => ['required', Rule::in(['Activo', 'Pasivo'])],
            // AÑADIR VALIDACIÓN PARA sucursal_id
            'sucursal_id' => ['required', 'integer', 'exists:sucursales,id'],
        ]);
        $area->update($data);
        return redirect()->route('areas.index')->with('status', 'Area actualizada.');
    }

    public function destroy(Area $area)
    {
        $area->delete(); // soft delete (si lo habilitaste)
        return redirect()->route('areas.index')->with('status', 'Area eliminada.');
    }
}
