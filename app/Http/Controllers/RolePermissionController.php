<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $roles = Role::query()
            ->with('permissions')
            ->when(
                $user->hasRole('Administrador'),
                fn ($query) => $query->where('name', '!=', 'SuperUsuario')
            )
            ->orderBy('name')
            ->get();

        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        return view('roles-permisos.index', compact(
            'roles',
            'permissions'
        ));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'exists:permissions,name',
            ],
        ]);

        if (
            auth()->user()->hasRole('Administrador')
            && $validated['name'] === 'SuperUsuario'
        ) {
            abort(403);
        }

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('roles-permisos.index')
            ->with('success', 'Rol creado correctamente.');
    }

    public function updateRole(Request $request, Role $role)
    {
        if (
            auth()->user()->hasRole('Administrador')
            && $role->name === 'SuperUsuario'
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'exists:permissions,name',
            ],
        ]);

        if (
            auth()->user()->hasRole('Administrador')
            && $validated['name'] === 'SuperUsuario'
        ) {
            abort(403);
        }

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('roles-permisos.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroyRole(Role $role)
    {
        if (
            auth()->user()->hasRole('Administrador')
            && $role->name === 'SuperUsuario'
        ) {
            abort(403);
        }

        $role->delete();

        return redirect()
            ->route('roles-permisos.index')
            ->with('success', 'Rol eliminado correctamente.');
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles-permisos.index')
            ->with('success', 'Permiso creado correctamente.');
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
        ]);

        $permission->update([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('roles-permisos.index')
            ->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route('roles-permisos.index')
            ->with('success', 'Permiso eliminado correctamente.');
    }
}
