<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdministrativePasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    private function actorWithUpdatePermission(): User
    {
        $role = Role::query()->create(['name' => 'Password admin', 'slug' => 'password-admin', 'is_system' => false]);
        $role->permissions()->sync(Permission::query()->where('slug', 'users.update')->pluck('id'));
        $actor = User::factory()->create();
        $actor->roles()->attach($role);

        return $actor;
    }

    public function test_authorized_admin_can_reset_another_users_password_without_auditing_secret(): void
    {
        $actor = $this->actorWithUpdatePermission();
        $managed = User::factory()->create(['password' => Hash::make('Original123!')]);

        $response = $this->actingAs($actor)->post(route('users.password.reset', $managed));

        $response->assertRedirect(route('users.index'))->assertSessionHas('temporary_password');
        $temporary = session('temporary_password');

        $this->assertTrue(Hash::check($temporary, $managed->fresh()->password));
        $this->assertTrue($managed->fresh()->must_change_password);

        $audit = AuditEvent::query()->where('event', 'user.password_reset')->latest('id')->firstOrFail();
        $this->assertStringNotContainsString($temporary, json_encode($audit->metadata));
    }

    public function test_user_without_permission_cannot_reset_password(): void
    {
        $managed = User::factory()->create();
        $original = $managed->password;

        $this->actingAs(User::factory()->create())
            ->post(route('users.password.reset', $managed))
            ->assertForbidden();

        $this->assertSame($original, $managed->fresh()->password);
    }

    public function test_temporary_password_forces_change_before_application_access(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('password.change'));

        $this->actingAs($user)
            ->get(route('password.change'))
            ->assertOk()
            ->assertSee('Define una nueva contraseña');

        $this->actingAs($user)
            ->put(route('password.update'), [
                'password' => 'NuevaClave123!',
                'password_confirmation' => 'NuevaClave123!',
            ])
            ->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('NuevaClave123!', $user->password));
    }

    public function test_public_forgot_password_route_is_not_exposed(): void
    {
        $this->get('/forgot-password')->assertNotFound();
    }
}
