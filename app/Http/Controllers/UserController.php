<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('users.view');

        return view('users.index', [
            'users' => User::query()->with('roles')->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('users.create');

        $canManageRoles = $request->user()->can('roles.manage');

        return view('users.create', [
            'canManageRoles' => $canManageRoles,
            'roles' => $canManageRoles ? Role::query()->orderBy('name')->get() : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('users.create');

        $canManageRoles = $request->user()->can('roles.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => [$canManageRoles ? 'required' : 'prohibited', 'integer', Rule::exists('roles', 'id')],
        ]);

        DB::transaction(function () use ($validated, $canManageRoles): void {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            if ($canManageRoles) {
                $user->roles()->sync([$validated['role_id']]);
            }
        });

        return redirect()->route('users.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorize('users.update');

        $canManageRoles = $request->user()->can('roles.manage');

        return view('users.edit', [
            'managedUser' => $user->load('roles'),
            'canManageRoles' => $canManageRoles,
            'roles' => $canManageRoles ? Role::query()->orderBy('name')->get() : collect(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('users.update');

        $canManageRoles = $request->user()->can('roles.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => [$canManageRoles ? 'required' : 'prohibited', 'integer', Rule::exists('roles', 'id')],
        ]);

        DB::transaction(function () use ($validated, $user, $canManageRoles): void {
            $attributes = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (! empty($validated['password'])) {
                $attributes['password'] = Hash::make($validated['password']);
            }

            $user->update($attributes);

            if ($canManageRoles) {
                $user->roles()->sync([$validated['role_id']]);
            }
        });

        return redirect()->route('users.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        $this->authorize('users.deactivate');

        if ($request->user()->is($user)) {
            return redirect()->route('users.index')->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('users.index')->with(
            'status',
            $user->is_active ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.'
        );
    }
}
