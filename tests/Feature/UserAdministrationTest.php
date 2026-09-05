<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    private function userWithPermissions(array $permissions): User
    {
        $role = Role::query()->create([
            'name' => 'Rol de prueba '.implode(', ', $permissions),
            'slug' => 'test-'.substr(sha1(implode('|', $permissions).uniqid('', true)), 0, 16),
            'is_system' => false,
        ]);

        $permissionIds = Permission::query()
            ->whereIn('slug', $permissions)
            ->pluck('id');

        $role->permissions()->sync($permissionIds);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    private function userWithPermission(string $permission): User
    {
        return $this->userWithPermissions([$permission]);
    }

    public function test_guest_cannot_access_user_administration(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_user_with_view_permission_can_list_users(): void
    {
        $user = $this->userWithPermission('users.view');

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_authorized_user_can_create_user_and_assign_role_when_role_management_is_allowed(): void
    {
        $actor = $this->userWithPermissions(['users.create', 'roles.manage']);
        $role = Role::query()->where('slug', 'therapist')->firstOrFail();

        $response = $this->actingAs($actor)->post(route('users.store'), [
            'name' => 'Terapeuta Prueba',
            'email' => 'terapeuta@urpe.test',
            'password' => 'UrpeTest123!',
            'password_confirmation' => 'UrpeTest123!',
            'role_id' => $role->id,
        ]);

        $response->assertRedirect(route('users.index'));
        $created = User::query()->where('email', 'terapeuta@urpe.test')->firstOrFail();
        $this->assertTrue($created->hasRole('therapist'));
        $this->assertTrue($created->is_active);
    }

    public function test_user_with_create_permission_but_without_role_management_creates_account_without_role(): void
    {
        $actor = $this->userWithPermission('users.create');

        $this->actingAs($actor)->post(route('users.store'), [
            'name' => 'Cuenta sin rol',
            'email' => 'sin-rol@urpe.test',
            'password' => 'UrpeTest123!',
            'password_confirmation' => 'UrpeTest123!',
        ])->assertRedirect(route('users.index'));

        $created = User::query()->where('email', 'sin-rol@urpe.test')->firstOrFail();
        $this->assertCount(0, $created->roles);
    }

    public function test_user_without_role_management_cannot_smuggle_role_assignment_on_create(): void
    {
        $actor = $this->userWithPermission('users.create');
        $role = Role::query()->where('slug', 'therapist')->firstOrFail();

        $this->actingAs($actor)->post(route('users.store'), [
            'name' => 'Intento de rol',
            'email' => 'intento-rol@urpe.test',
            'password' => 'UrpeTest123!',
            'password_confirmation' => 'UrpeTest123!',
            'role_id' => $role->id,
        ])->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'intento-rol@urpe.test']);
    }

    public function test_user_without_create_permission_cannot_create_accounts(): void
    {
        $role = Role::query()->where('slug', 'therapist')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->post(route('users.store'), [
                'name' => 'No autorizado',
                'email' => 'no-autorizado@urpe.test',
                'password' => 'UrpeTest123!',
                'password_confirmation' => 'UrpeTest123!',
                'role_id' => $role->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'no-autorizado@urpe.test']);
    }

    public function test_authorized_user_can_update_account_and_role_when_role_management_is_allowed(): void
    {
        $actor = $this->userWithPermissions(['users.update', 'roles.manage']);
        $managed = User::factory()->create();
        $role = Role::query()->where('slug', 'reception')->firstOrFail();

        $this->actingAs($actor)
            ->put(route('users.update', $managed), [
                'name' => 'Recepción URPE',
                'email' => 'recepcion@urpe.test',
                'password' => '',
                'password_confirmation' => '',
                'role_id' => $role->id,
            ])
            ->assertRedirect(route('users.index'));

        $managed->refresh();
        $this->assertSame('Recepción URPE', $managed->name);
        $this->assertTrue($managed->hasRole('reception'));
    }

    public function test_user_without_role_management_can_update_account_without_changing_existing_role(): void
    {
        $actor = $this->userWithPermission('users.update');
        $managed = User::factory()->create();
        $managed->assignRole('therapist');

        $this->actingAs($actor)
            ->put(route('users.update', $managed), [
                'name' => 'Terapeuta Actualizado',
                'email' => 'terapeuta-actualizado@urpe.test',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('users.index'));

        $managed->refresh();
        $this->assertSame('Terapeuta Actualizado', $managed->name);
        $this->assertTrue($managed->hasRole('therapist'));
    }

    public function test_user_without_role_management_cannot_smuggle_role_change_on_update(): void
    {
        $actor = $this->userWithPermission('users.update');
        $managed = User::factory()->create();
        $managed->assignRole('therapist');
        $reception = Role::query()->where('slug', 'reception')->firstOrFail();

        $this->actingAs($actor)
            ->put(route('users.update', $managed), [
                'name' => $managed->name,
                'email' => $managed->email,
                'password' => '',
                'password_confirmation' => '',
                'role_id' => $reception->id,
            ])
            ->assertSessionHasErrors('role_id');

        $managed->refresh();
        $this->assertTrue($managed->hasRole('therapist'));
        $this->assertFalse($managed->hasRole('reception'));
    }

    public function test_authorized_user_can_deactivate_and_reactivate_another_account(): void
    {
        $actor = $this->userWithPermission('users.deactivate');
        $managed = User::factory()->create(['is_active' => true]);

        $this->actingAs($actor)
            ->patch(route('users.toggle-active', $managed))
            ->assertRedirect(route('users.index'));

        $this->assertFalse($managed->fresh()->is_active);

        $this->actingAs($actor)
            ->patch(route('users.toggle-active', $managed))
            ->assertRedirect(route('users.index'));

        $this->assertTrue($managed->fresh()->is_active);
    }

    public function test_user_without_deactivate_permission_cannot_change_account_status(): void
    {
        $managed = User::factory()->create(['is_active' => true]);

        $this->actingAs(User::factory()->create())
            ->patch(route('users.toggle-active', $managed))
            ->assertForbidden();

        $this->assertTrue($managed->fresh()->is_active);
    }

    public function test_user_cannot_deactivate_own_account(): void
    {
        $actor = $this->userWithPermission('users.deactivate');

        $this->actingAs($actor)
            ->patch(route('users.toggle-active', $actor))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('error', 'No puedes desactivar tu propia cuenta.');

        $this->assertTrue($actor->fresh()->is_active);
    }
}
