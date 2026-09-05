<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('roles.view');

        return view('roles.index', [
            'roles' => Role::query()
                ->withCount(['users', 'permissions'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function edit(Role $role): View
    {
        $this->authorize('roles.manage');

        return view('roles.edit', [
            'managedRole' => $role->load('permissions'),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('roles.manage');

        $validated = $request->validate([
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Permisos del rol actualizados correctamente.');
    }
}
