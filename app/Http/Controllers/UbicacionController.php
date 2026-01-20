<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UbicacionController extends Controller
{
    public function __construct()
    {
        // Middleware de permisos - CORREGIDO: usar 'ubicaciones' en lugar de 'areas'
        $this->middleware(['permission:ubicaciones.view'])->only(['index', 'show']);
        $this->middleware(['permission:ubicaciones.create'])->only(['create', 'store']);
        $this->middleware(['permission:ubicaciones.update'])->only(['edit', 'update']);
        $this->middleware(['permission:ubicaciones.delete'])->only(['destroy']);
    }

    /**
     * Muestra la lista de ubicaciones.
     */
    public function index()
    {
        $ubicaciones = Ubicacion::with('area.sucursal')
            ->orderBy('descripcion')
            ->get();

        return view('ubicaciones.index', compact('ubicaciones'));
    }

    /**
     * Muestra el formulario para crear una nueva ubicación.
     */
    public function create()
    {
        $areas = Area::with('sucursal')
            ->where('estado', 'Activo')
            ->orderBy('descripcion')
            ->get();

        return view('ubicaciones.create', compact('areas'));
    }

    /**
     * Almacena una nueva ubicación en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:150|unique:ubicacion,descripcion',
            'estado'      => 'required|in:Activo,Pasivo',
            'area_id'     => 'required|exists:areas,id',
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.unique'   => 'Ya existe una ubicación con esta descripción.',
            'area_id.required'     => 'Debe seleccionar un área.',
            'area_id.exists'       => 'El área seleccionada no es válida.',
        ]);

        Ubicacion::create($validated);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación creada correctamente.');
    }

    /**
     * Muestra una ubicación específica.
     */
    public function show(Ubicacion $ubicacion)
    {
        $ubicacion->load('area.sucursal');
        return view('ubicaciones.show', compact('ubicacion'));
    }

    /**
     * Muestra el formulario para editar la ubicación.
     */
    public function edit(Ubicacion $ubicacion)
    {
        $areas = Area::with('sucursal')
            ->where('estado', 'Activo')
            ->orderBy('descripcion')
            ->get();

        return view('ubicaciones.edit', compact('ubicacion', 'areas'));
    }

    /**
     * Actualiza la ubicación en la base de datos.
     */
    public function update(Request $request, Ubicacion $ubicacion)
    {
        $validated = $request->validate([
            'descripcion' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ubicacion')->ignore($ubicacion->id),
            ],
            'estado'  => 'required|in:Activo,Pasivo',
            'area_id' => 'required|exists:areas,id',
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.unique'   => 'Ya existe una ubicación con esta descripción.',
            'area_id.required'     => 'Debe seleccionar un área.',
            'area_id.exists'       => 'El área seleccionada no es válida.',
        ]);

        $ubicacion->update($validated);

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    /**
     * Elimina la ubicación (Soft Delete).
     */
    public function destroy(Ubicacion $ubicacion)
    {
        try {
            $ubicacion->delete();

            return redirect()->route('ubicaciones.index')
                ->with('success', 'Ubicación eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ubicaciones.index')
                ->with('error', 'No se pudo eliminar la ubicación. Puede estar en uso.');
        }
    }
}
