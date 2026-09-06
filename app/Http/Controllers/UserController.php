<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditTrail;
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

    public function store(Request $request, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('users.create');

        $canManageRoles = $request->user()->can('roles.manage');

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role_id' => [$canManageRoles ? 'required' : 'prohibited', 'integer', Rule::exists('roles', 'id')],
            ],
            $this->validationMessages(),
            $this->validationAttributes(),
        );

        DB::transaction(function () use ($validated, $canManageRoles, $request, $audit): void {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            if ($canManageRoles) {
                $user->roles()->sync([$validated['role_id']]);
            }

            $audit->record('user.created', $user, [
                'role_id' => $canManageRoles ? $validated['role_id'] : null,
                'is_active' => true,
            ], $request->user(), $request);
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

    public function update(Request $request, User $user, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('users.update');

        $canManageRoles = $request->user()->can('roles.manage');

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'role_id' => [$canManageRoles ? 'required' : 'prohibited', 'integer', Rule::exists('roles', 'id')],
            ],
            $this->validationMessages(),
            $this->validationAttributes(),
        );

        DB::transaction(function () use ($validated, $user, $canManageRoles, $request, $audit): void {
            $before = [
                'name' => $user->name,
                'email' => $user->email,
                'role_ids' => $user->roles()->pluck('roles.id')->all(),
            ];

            $attributes = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            $passwordChanged = ! empty($validated['password']);

            if ($passwordChanged) {
                $attributes['password'] = Hash::make($validated['password']);
            }

            $user->update($attributes);

            if ($canManageRoles) {
                $user->roles()->sync([$validated['role_id']]);
            }

            $audit->record('user.updated', $user, [
                'before' => $before,
                'after' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_ids' => $user->roles()->pluck('roles.id')->all(),
                ],
                'password_changed' => $passwordChanged,
            ], $request->user(), $request);
        });

        return redirect()->route('users.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleActive(Request $request, User $user, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('users.deactivate');

        if ($request->user()->is($user)) {
            $audit->record('user.status_change_self_blocked', $user, [
                'requested_is_active' => ! $user->is_active,
            ], $request->user(), $request);

            return redirect()->route('users.index')->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        DB::transaction(function () use ($request, $user, $audit): void {
            $previous = $user->is_active;
            $user->update(['is_active' => ! $previous]);

            $audit->record(
                $user->is_active ? 'user.activated' : 'user.deactivated',
                $user,
                [
                    'before' => ['is_active' => $previous],
                    'after' => ['is_active' => $user->is_active],
                ],
                $request->user(),
                $request,
            );
        });

        return redirect()->route('users.index')->with(
            'status',
            $user->is_active ? 'Usuario activado correctamente.' : 'Usuario desactivado correctamente.'
        );
    }

    private function validationMessages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'email' => 'Ingresa un correo electrónico válido.',
            'unique' => 'Ese :attribute ya está registrado.',
            'min' => 'La :attribute debe tener al menos :min caracteres.',
            'confirmed' => 'La confirmación de :attribute no coincide.',
            'integer' => 'El campo :attribute no es válido.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'prohibited' => 'No tienes permiso para enviar el campo :attribute.',
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'role_id' => 'rol',
        ];
    }
}
