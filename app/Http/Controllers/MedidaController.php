<?php

namespace App\Http\Controllers;

use App\Models\Medida;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedidaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:medidas.view'])->only(['index','show']);
        $this->middleware(['permission:medidas.create'])->only(['create','store']);
        $this->middleware(['permission:medidas.update'])->only(['edit','update']);
        $this->middleware(['permission:medidas.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $query = Medida::query();
        if ($q !== '') {
            $query->where(function($qq) use ($q) {
                $qq->where('descripcion','like',"%{$q}%")
                   ->orWhere('simbolo','like',"%{$q}%");
            });
        }
        $medidas = $query->orderBy('descripcion')->paginate(20)->withQueryString();
        return view('medidas.index', compact('medidas'));
    }

    public function create()
    {
        return view('medidas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => ['required','string','max:150','unique:medidas,descripcion'],
            'simbolo'     => ['required','string','max:20','unique:medidas,simbolo'],
        ]);
        Medida::create($data);
        return redirect()->route('medidas.index')->with('status','Medida creada.');
    }

    public function show(Medida $medida)
    {
        return view('medidas.show', compact('medida'));
    }

    public function edit(Medida $medida)
    {
        return view('medidas.edit', compact('medida'));
    }

    public function update(Request $request, Medida $medida)
    {
        $data = $request->validate([
            'descripcion' => ['required','string','max:150', Rule::unique('medidas','descripcion')->ignore($medida->id)],
            'simbolo'     => ['required','string','max:20',  Rule::unique('medidas','simbolo')->ignore($medida->id)],
        ]);
        $medida->update($data);
        return redirect()->route('medidas.index')->with('status','Medida actualizada.');
    }

    public function destroy(Medida $medida)
    {
        $medida->delete();
        return redirect()->route('medidas.index')->with('status','Medida eliminada.');
    }
}
