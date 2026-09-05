<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    private function userWithPermission(string $permission): User
    {
        $role = Role::query()->create([
            'name' => 'Rol de prueba '.$permission,
            'slug' => 'role-admin-'.str_replace('.', '-', $permission),
            'is_system' => false,
        ]);

        $role->permissions()->attach(
            Permission::query()->where('slug', $permission)->firstOrFail()
        );

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    public function test_guest_cannot_access_roles(): void
    {
        $this->get(route('roles.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_roles_view_permission_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_user_with_roles_view_permission_can_list_roles(): void
    {
        $user = $this->userWithPermission('roles.view');

        $this->actingAs($user)
            ->get(route('roles.index'))
            ->assertOk()
            ->assertSee('Administrador')
            ->assertSee('Terapeuta');
    }

    public function test_user_without_roles_manage_permission_cannot_edit_role_permissions(): void
    {
        $user = $this->userWithPermission('roles.view');
        $role = Role::query()->where('slug', 'therapist')->firstOrFail();

        $this->actingAs($user)
            ->get(route('roles.edit', $role))
            ->assertForbidden();
    }

    public function test_authorized_user_can_replace_role_permissions(): void
    {
        $user = $this->userWithPermission('roles.manage');
        $role = Role::query()->where('slug', 'reception')->firstOrFail();
        $viewUsers = Permission::query()->where('slug', 'users.view')->firstOrFail();
        $createUsers = Permission::query()->where('slug', 'users.create')->firstOrFail();

        $role->permissions()->sync([$viewUsers->id]);

        $this->actingAs($user)
            ->put(route('roles.update', $role), [
                'permissions' => [$createUsers->id],
            ])
            ->assertRedirect(route('roles.index'));

        $role->refresh();
        $this->assertFalse($role->permissions()->whereKey($viewUsers->id)->exists());
        $this->assertTrue($role->permissions()->whereKey($createUsers->id)->exists());
    }

    public function test_role_permissions_can_be_cleared(): void
    {
        $user = $this->userWithPermission('roles.manage');
        $role = Role::query()->where('slug', 'consultation_direction')->firstOrFail();
        $permission = Permission::query()->where('slug', 'users.view')->firstOrFail();
        $role->permissions()->sync([$permission->id]);

        $this->actingAs($user)
            ->put(route('roles.update', $role), [])
            ->assertRedirect(route('roles.index'));

        $this->assertSame(0, $role->permissions()->count());
    }
}
