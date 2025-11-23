<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function __construct()
    {
        // Descomenta si tienes seed-eados estos permisos
        $this->middleware(['permission:categorias.view'])->only(['index', 'show']);
        $this->middleware(['permission:categorias.create'])->only(['create', 'store']);
        $this->middleware(['permission:categorias.update'])->only(['edit', 'update']);
        $this->middleware(['permission:categorias.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q = trim($request->input('q'));
        $estado = $request->input('estado'); // 'Activo' | 'Pasivo' | null

        $base = \App\Models\Categoria::query()
            ->when($q, fn($qb) => $qb->where('descripcion', 'like', "%{$q}%"))
            ->when($estado, fn($qb) => $qb->where('estado', $estado));

        $total   = (clone $base)->count();
        $activos = (clone $base)->where('estado', 'Activo')->count();
        $pasivos = $total - $activos;

        $categorias = (clone $base)
            ->orderBy('descripcion')
            ->paginate(20)
            ->withQueryString();

        return view('categorias.index', compact('categorias', 'total', 'activos', 'pasivos'));
    }


    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => ['required', 'string', 'max:150', 'unique:categorias,descripcion'],
            'estado'      => ['required', Rule::in(['Activo', 'Pasivo'])],
        ]);
        Categoria::create($data);
        return redirect()->route('categorias.index')->with('status', 'Categoría creada.');
    }

    public function show(Categoria $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'descripcion' => ['required', 'string', 'max:150', Rule::unique('categorias', 'descripcion')->ignore($categoria->id)],
            'estado'      => ['required', Rule::in(['Activo', 'Pasivo'])],
        ]);
        $categoria->update($data);
        return redirect()->route('categorias.index')->with('status', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete(); // soft delete (si lo habilitaste)
        return redirect()->route('categorias.index')->with('status', 'Categoría eliminada.');
    }
}
