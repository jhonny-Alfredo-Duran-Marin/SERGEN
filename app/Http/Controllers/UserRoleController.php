<?php
// app/Http/Controllers/UserRoleController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(20);
        return view('usuarios.user.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $selected = $user->roles()->pluck('id')->toArray();
        return view('usuarios.user.edit', compact('user','roles','selected'));
    }

     public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Convertir IDs -> modelos Role (guard 'web')
        $roleIds = array_map('intval', $data['roles'] ?? []);
        $roles = Role::whereIn('id', $roleIds)->get();

        // Sincronizar con modelos (evita "There is no role named `2`")
        $user->syncRoles($roles);

        app('cache')->forget('spatie.permission.cache');

        return redirect()->route('users.index')->with('status','Roles actualizados para el usuario');
    }
}
