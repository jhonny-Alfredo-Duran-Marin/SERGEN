<?php

namespace App\Http\Controllers;

use App\Models\Dotacion;
use App\Models\Item;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DotacionController extends Controller
{
    public function __construct()
    {
        // Ajusta permisos si usas spatie/permission
        $this->middleware(['permission:dotaciones.view'])->only(['index','show']);
        $this->middleware(['permission:dotaciones.create'])->only(['create','store']);
        $this->middleware(['permission:dotaciones.update'])->only(['edit','update']);
        $this->middleware(['permission:dotaciones.delete'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $q       = $request->string('q')->toString();
        $itemId  = $request->integer('item_id');
        $persId  = $request->integer('persona_id');
        $f1      = $request->date('desde');
        $f2      = $request->date('hasta');

        $query = Dotacion::query()
            ->with(['item:id,codigo,descripcion,imagen_thumb,imagen_path','persona:id,nombre'])
            ->orderByDesc('fecha')->orderByDesc('id');

        if ($q !== '') {
            $query->whereHas('item', function($qq) use ($q){
                $qq->where('codigo','like',"%{$q}%")
                   ->orWhere('descripcion','like',"%{$q}%");
            })->orWhereHas('persona', function($qq) use ($q){
                $qq->where('nombre','like',"%{$q}%");
            });
        }
        if ($itemId)  $query->where('item_id',$itemId);
        if ($persId)  $query->where('persona_id',$persId);
        if ($f1)      $query->whereDate('fecha','>=',$f1);
        if ($f2)      $query->whereDate('fecha','<=',$f2);

        $dotaciones = $query->paginate(20)->withQueryString();

        // métricas
        $total = (clone $query)->count();
        $totalUnidades = (clone $query)->sum('cantidad');

        $items    = Item::orderBy('descripcion')->get(['id','codigo','descripcion']);
        $personas = Persona::orderBy('nombre')->get(['id','nombre']);

        return view('dotaciones.index', compact('dotaciones','items','personas','total','totalUnidades'));
    }

    public function create()
    {
        $items    = Item::orderBy('descripcion')->get(['id','codigo','descripcion','cantidad']);
        $personas = Persona::orderBy('nombre')->get(['id','nombre']);
        return view('dotaciones.create', compact('items','personas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id'    => ['required','exists:items,id'],
            'persona_id' => ['required','exists:personas,id'],
            'cantidad'   => ['required','integer','min:1'],
            'fecha'      => ['required','date'],
        ]);

        DB::transaction(function() use ($data) {
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            if ($data['cantidad'] > $item->cantidad) {
                abort(422, 'No hay stock suficiente del ítem seleccionado.');
            }

            Dotacion::create($data);
            $item->decrement('cantidad', $data['cantidad']);
        });

        return redirect()->route('dotaciones.index')->with('status','Dotación registrada.');
    }

    public function show(Dotacion $dotacione) // por convención de resource en singular del nombre
    {
        $dotacione->load(['item','persona']);
        return view('dotaciones.show', compact('dotacione'));
    }

    public function edit(Dotacion $dotacione)
    {
        $items    = Item::orderBy('descripcion')->get(['id','codigo','descripcion','cantidad']);
        $personas = Persona::orderBy('nombre')->get(['id','nombre']);
        return view('dotaciones.edit', compact('dotacione','items','personas'));
    }

    public function update(Request $request, Dotacion $dotacione)
    {
        $data = $request->validate([
            'item_id'    => ['required','exists:items,id'],
            'persona_id' => ['required','exists:personas,id'],
            'cantidad'   => ['required','integer','min:1'],
            'fecha'      => ['required','date'],
        ]);

        DB::transaction(function() use ($dotacione,$data) {
            // Si cambia de item, revertimos stock anterior y descontamos del nuevo
            if ($dotacione->item_id !== (int)$data['item_id']) {
                $oldItem = Item::lockForUpdate()->findOrFail($dotacione->item_id);
                $oldItem->increment('cantidad', $dotacione->cantidad);

                $newItem = Item::lockForUpdate()->findOrFail($data['item_id']);
                if ($data['cantidad'] > $newItem->cantidad) {
                    abort(422, 'No hay stock suficiente del nuevo ítem.');
                }
                $newItem->decrement('cantidad', $data['cantidad']);
            } else {
                // mismo item: ajustamos diferencia
                $item = Item::lockForUpdate()->findOrFail($dotacione->item_id);
                $diff = (int)$data['cantidad'] - (int)$dotacione->cantidad;
                if ($diff > 0) {
                    // quieren más: verificar stock
                    if ($diff > $item->cantidad) abort(422,'Stock insuficiente para incrementar la dotación.');
                    $item->decrement('cantidad', $diff);
                } elseif ($diff < 0) {
                    // devolvemos la diferencia
                    $item->increment('cantidad', abs($diff));
                }
            }

            $dotacione->update($data);
        });

        return redirect()->route('dotaciones.index')->with('status','Dotación actualizada.');
    }

    public function destroy(Dotacion $dotacione)
    {
        DB::transaction(function() use ($dotacione) {
            // reponer stock
            $item = Item::lockForUpdate()->findOrFail($dotacione->item_id);
            $item->increment('cantidad', $dotacione->cantidad);
            $dotacione->delete();
        });

        return redirect()->route('dotaciones.index')->with('status','Dotación eliminada.');
    }
}
