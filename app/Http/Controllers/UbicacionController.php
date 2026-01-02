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
        // Middleware de permisos (Spatie)
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
        // Cargamos área y la sucursal del área de una vez
        $ubicaciones = Ubicacion::with('area.sucursal')->get();
        return view('ubicaciones.index', compact('ubicaciones'));
    }
    /**
     * Muestra el formulario para crear una nueva ubicación.
     */
    public function create()
    {
        // Necesitamos las áreas para el select del formulario
        $areas = Area::all();
        return view('ubicaciones.create', compact('areas'));
    }

    /**
     * Almacena una nueva ubicación en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:150|unique:ubicacion,descripcion',
            'estado'      => 'required|in:Activo,Pasivo',
            'area_id'     => 'required|exists:areas,id',
        ]);

        Ubicacion::create($request->all());

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación creada correctamente.');
    }

    /**
     * Muestra una ubicación específica.
     */
    public function show(Ubicacion $ubicacion)
    {
        return view('ubicaciones.show', compact('ubicacion'));
    }

    /**
     * Muestra el formulario para editar la ubicación.
     */
    public function edit(Ubicacion $ubicacion)
    {
        $areas = Area::all();
        return view('ubicaciones.edit', compact('ubicacion', 'areas'));
    }

    /**
     * Actualiza la ubicación en la base de datos.
     */
    // El nombre debe ser $ubicacion porque así lo definió Laravel en la ruta
    public function update(Request $request, Ubicacion $ubicacion)
    {
        $request->validate([
            'descripcion' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ubicacion')->ignore($ubicacion->id),
            ],
            'estado'  => 'required|in:Activo,Pasivo',
            'area_id' => 'required|exists:areas,id',
        ]);

        $ubicacion->update($request->all());

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    /**
     * Elimina la ubicación (Soft Delete).
     */
    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();

        return redirect()->route('ubicaciones.index')
            ->with('success', 'Ubicación eliminada correctamente.');
    }
}
