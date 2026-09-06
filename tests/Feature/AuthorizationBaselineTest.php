<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthorizationBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorization_seeder_creates_initial_roles_and_permissions(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $this->assertDatabaseCount('roles', 5);
        $this->assertDatabaseCount('permissions', 8);

        foreach ([
            'administrator',
            'clinical_coordination',
            'therapist',
            'reception',
            'consultation_direction',
        ] as $role) {
            $this->assertDatabaseHas('roles', ['slug' => $role]);
        }

        $this->assertDatabaseHas('permissions', ['slug' => 'clinical_files.download']);
        $this->assertDatabaseHas('permissions', ['slug' => 'center.manage']);

        $administrator = Role::query()->where('slug', 'administrator')->firstOrFail();

        $this->assertCount(8, $administrator->permissions);
    }

    public function test_user_inherits_permissions_from_assigned_role(): void
    {
        $permission = Permission::query()->create([
            'name' => 'Ver usuarios',
            'slug' => 'users.view',
        ]);

        $role = Role::query()->create([
            'name' => 'Coordinación Clínica',
            'slug' => 'clinical_coordination',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('clinical_coordination'));
        $this->assertTrue($user->hasPermission('users.view'));
        $this->assertFalse($user->hasPermission('users.update'));
    }

    public function test_sync_roles_replaces_previous_assignments(): void
    {
        $first = Role::query()->create([
            'name' => 'Recepción',
            'slug' => 'reception',
        ]);

        $second = Role::query()->create([
            'name' => 'Terapeuta',
            'slug' => 'therapist',
        ]);

        $user = User::factory()->create();
        $user->assignRole($first);
        $user->syncRoles([$second]);

        $this->assertFalse($user->hasRole('reception'));
        $this->assertTrue($user->hasRole('therapist'));
        $this->assertDatabaseCount('role_user', 1);
    }

    public function test_laravel_gate_uses_granular_permissions(): void
    {
        $permission = Permission::query()->create([
            'name' => 'Actualizar usuarios',
            'slug' => 'users.update',
        ]);

        $role = Role::query()->create([
            'name' => 'Administrador',
            'slug' => 'administrator',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        $this->assertTrue(Gate::forUser($user)->allows('users.update'));
        $this->assertFalse(Gate::forUser($user)->allows('roles.manage'));
    }

    public function test_non_administrator_roles_are_not_overgranted_by_default(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('therapist');

        $this->assertFalse($user->hasPermission('users.view'));
        $this->assertFalse($user->hasPermission('roles.manage'));
        $this->assertFalse($user->hasPermission('clinical_files.download'));
        $this->assertFalse($user->hasPermission('center.manage'));
        $this->assertFalse(Gate::forUser($user)->allows('users.view'));
    }
}
