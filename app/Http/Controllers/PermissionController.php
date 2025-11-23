<?php
// app/Http/Controllers/PermissionController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q'));
        $permissions = Permission::query()
            ->when($q, fn($qb) => $qb->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(20);

        return view('usuarios.permisos.index', compact('permissions'));
    }
}
