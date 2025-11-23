<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Proyecto;
use Faker\Provider\ar_EG\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProyectosController extends Controller
{
    public function __construct()
    {
        // Quita estos middleware si aún no seed-easte proyectos.*
        $this->middleware(['permission:proyectos.view'])->only(['index', 'show']);
        $this->middleware(['permission:proyectos.create'])->only(['create', 'store']);
        $this->middleware(['permission:proyectos.update'])->only(['edit', 'update']);
        $this->middleware(['permission:proyectos.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        // Filtros simples: ?q=&estado=Abierto|Cerrado&facturado=1|0
        $q         = $request->string('q')->toString();
        $estado    = $request->string('estado')->toString();
        $facturado = $request->has('facturado') ? $request->boolean('facturado') : null;

        $query = Proyecto::query();

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('codigo', 'like', "%{$q}%")
                    ->orWhere('empresa', 'like', "%{$q}%")
                    ->orWhere('sitio', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        if (in_array($estado, ['Abierto', 'Cerrado'], true)) {
            $query->where('estado', $estado);
        }

        if ($facturado !== null) {
            $query->where('es_facturado', $facturado);
        }

        $proyectos = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $personas = Persona::all();
        return view('proyectos.create', compact('personas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'        => ['required', 'string', 'max:50', 'unique:proyectos,codigo'],
            'descripcion'   => ['required', 'string'],
            'empresa'       => ['required', 'string', 'max:150'],
            'orden_compra'  => ['nullable', 'string', 'max:100'],
            'sitio'         => ['nullable', 'string', 'max:150'],
            'monto'         => ['required', 'numeric', 'min:0', 'max:99999999999.99'],
            'es_facturado'  => ['nullable', 'boolean'],
            'estado'        => ['required', Rule::in(['Abierto', 'Cerrado'])],
            'fecha_inicio'  => ['nullable', 'date'],
            'fecha_fin'     => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'persona_id'    => ['required', 'exists:personas,id'],
        ]);

        $data['es_facturado'] = $request->boolean('es_facturado');

        Proyecto::create($data);

        return redirect()->route('proyectos.index')->with('status', 'Proyecto creado correctamente.');
    }

    public function show(Proyecto $proyecto)
    {
        return view('proyectos.show', compact('proyecto'));
    }

    public function edit(Proyecto $proyecto)
    {
         $personas = Persona::all();
        return view('proyectos.edit', compact('proyecto','personas'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'codigo'        => ['required', 'string', 'max:50', Rule::unique('proyectos', 'codigo')->ignore($proyecto->id)],
            'descripcion'   => ['required', 'string'],
            'empresa'       => ['required', 'string', 'max:150'],
            'orden_compra'  => ['nullable', 'string', 'max:100'],
            'sitio'         => ['nullable', 'string', 'max:150'],
            'monto'         => ['required', 'numeric', 'min:0', 'max:99999999999.99'],
            'es_facturado'  => ['nullable', 'boolean'],
            'estado'        => ['required', Rule::in(['Abierto', 'Cerrado'])],
            'fecha_inicio'  => ['nullable', 'date'],
            'fecha_fin'     => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'persona_id'    => ['nullable', 'exists:personas,id'],
        ]);

        $data['es_facturado'] = $request->boolean('es_facturado');

        $proyecto->update($data);

        return redirect()->route('proyectos.index')->with('status', 'Proyecto actualizado.');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete(); // Soft delete
        return redirect()->route('proyectos.index')->with('status', 'Proyecto eliminado.');
    }
}
