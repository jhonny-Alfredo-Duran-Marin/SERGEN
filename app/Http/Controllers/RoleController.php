<?php
// app/Http/Controllers/RoleController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->paginate(20);
        return view('usuarios.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('usuarios.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = \Spatie\Permission\Models\Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        // Cargar modelos Permission por ID y sincronizar
        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        $role->syncPermissions($perms);

        app('cache')->forget('spatie.permission.cache');

        return redirect()->route('roles.index')->with('status', 'Rol creado');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $selected = $role->permissions()->pluck('id')->toArray();
        return view('usuarios.roles.edit', compact('role', 'permissions', 'selected'));
    }

    public function update(Request $request, \Spatie\Permission\Models\Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $data['name']]);

        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        $role->syncPermissions($perms);

        app('cache')->forget('spatie.permission.cache');

        return redirect()->route('roles.index')->with('status', 'Rol actualizado');
    }
    public function destroy(Role $role)
    {
        $role->delete();
        app()['cache']->forget('spatie.permission.cache');
        return redirect()->route('roles.index')->with('status', 'Rol eliminado');
    }
}
