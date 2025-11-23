<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class PersonaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:personas.view'])->only('index', 'show');
        $this->middleware(['auth', 'permission:personas.create'])->only('create', 'store');
        $this->middleware(['auth', 'permission:personas.update'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:personas.delete'])->only('destroy');
    }


    public function index(Request $request)
    {
        $q      = trim($request->input('q'));
        $estado = $request->input('estado');   // '1','0' o null
        $filter = $request->input('filter');   // 'con','sin' o null

        $base = Persona::query()
            ->with(['user.roles']) // evita N+1
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('celular', 'like', "%{$q}%")
                        ->orWhere('cargo', 'like', "%{$q}%");
                });
            })
            ->when($estado !== null && $estado !== '', fn($qb) => $qb->where('estado', (int) $estado))
            ->when($filter === 'con', fn($qb) => $qb->whereHas('user'))
            ->when($filter === 'sin', fn($qb) => $qb->whereDoesntHave('user'));

        // Métricas con los mismos filtros
        $total      = (clone $base)->count();
        $activos    = (clone $base)->where('estado', 1)->count();
        $conUsuario = (clone $base)->whereHas('user')->count();
        $sinUsuario = $total - $conUsuario;

        $personas = (clone $base)
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('personas.index', compact('personas', 'total', 'activos', 'conUsuario', 'sinUsuario'));
    }

    public function create()
    {
        // para checkboxes de roles al crear el usuario opcional
        $roles = Role::orderBy('name')->get(['id', 'name']);
        return view('personas.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'  => ['required', 'string', 'max:150'],
            'cargo'   => ['nullable', 'string', 'max:120'],
            'celular' => ['nullable', 'string', 'max:20', 'unique:personas,celular'],
            'estado'  => ['nullable', 'boolean'],
        ]);

        $persona = Persona::create([
            'nombre'  => $data['nombre'],
            'cargo'   => $data['cargo']   ?? null,
            'celular' => $data['celular'] ?? null,
            'estado'  => $data['estado']  ?? true,
        ]);

        // ¿Crear usuario asociado?
        if ($request->boolean('create_user')) {
            $u = $request->validate([
                'user_name'  => ['nullable', 'string', 'max:255'],
                'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
                'password'   => ['required', 'confirmed', 'min:8'],
                'roles'      => ['array'],
                'roles.*'    => ['exists:roles,id'],
            ]);

            $user = new User();
            $user->name              = $u['user_name'] ?? $data['nombre'];
            $user->email             = $u['email'];
            // Si tu modelo User tiene cast 'password' => 'hashed', NO uses Hash::make
            $user->password          = $u['password'];
            $user->persona_id        = $persona->id;
            $user->email_verified_at = now();
            $user->save();

            if (!empty($u['roles'])) {
                $roleModels = Role::whereIn('id', $u['roles'])->get();
                $user->syncRoles($roleModels);
            }
        }

        return redirect()->route('personas.index')->with('status', 'Persona creada correctamente.');
    }

    public function show(Persona $persona)
    {
        $persona->load(['user:id,persona_id,name,email', 'user.roles:id,name']);
        return view('personas.show', compact('persona')); // crea esta vista si la usarás
    }

    public function edit(Persona $persona)
    {
        $persona->load(['user:id,persona_id,name,email', 'user.roles:id,name']);
        $roles = Role::orderBy('name')->get(['id', 'name']); // para mostrar roles si quieres
        return view('personas.edit', compact('persona', 'roles'));
    }

    public function update(Request $request, Persona $persona)
    {
        $data = $request->validate([
            'nombre'  => ['required', 'string', 'max:150'],
            'cargo'   => ['nullable', 'string', 'max:120'],
            'celular' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('personas', 'celular')->ignore($persona->id),
            ],
            'estado'  => ['nullable', 'boolean'],
        ]);

        $persona->update([
            'nombre'  => $data['nombre'],
            'cargo'   => $data['cargo']   ?? null,
            'celular' => $data['celular'] ?? null,
            'estado'  => $data['estado']  ?? $persona->estado,
        ]);

        // (Opcional) actualizar roles del usuario si viene 'roles[]' y existe usuario
        if ($persona->user && $request->has('roles')) {
            $request->validate([
                'roles'   => ['array'],
                'roles.*' => ['exists:roles,id'],
            ]);
            $persona->user->syncRoles(Role::whereIn('id', $request->input('roles', []))->get());
        }

        return redirect()->route('personas.index')->with('status', 'Persona actualizada.');
    }

    public function destroy(Persona $persona)
    {
        // Si tu FK en users(persona_id) está como ->nullOnDelete(), no necesitas tocar al user
        $persona->delete();
        return redirect()->route('personas.index')->with('status', 'Persona eliminada.');
    }
}
