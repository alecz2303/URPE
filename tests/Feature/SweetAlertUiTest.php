<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SweetAlertUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_user_administration_renders_sweetalert_confirmation_baseline(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole('administrator');
        User::factory()->create();

        $this->actingAs($actor)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('sweetalert2@11', false)
            ->assertSee('data-swal-confirm', false);
    }

    public function test_role_administration_renders_sweetalert_feedback_baseline(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole('administrator');

        $this->actingAs($actor)
            ->withSession(['status' => 'Permisos actualizados.'])
            ->get(route('roles.index'))
            ->assertOk()
            ->assertSee('sweetalert2@11', false)
            ->assertSee('Permisos actualizados.', false);
    }

    public function test_user_creation_form_renders_sweetalert_validation_feedback(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole('administrator');
        $role = Role::query()->where('slug', 'therapist')->firstOrFail();

        $response = $this->actingAs($actor)
            ->from(route('users.create'))
            ->post(route('users.store'), [
                'name' => 'Operador de Prueba',
                'email' => 'prueba@mail.com',
                'role_id' => $role->id,
                'password' => '123',
                'password_confirmation' => '123',
            ]);

        $response
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password']);

        $this->followingRedirects()
            ->actingAs($actor)
            ->get(route('users.create'))
            ->assertSee('sweetalert2@11', false);
    }

    public function test_user_creation_validation_message_is_clear_spanish(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole('administrator');
        $role = Role::query()->where('slug', 'therapist')->firstOrFail();

        $this->actingAs($actor)
            ->from(route('users.create'))
            ->post(route('users.store'), [
                'name' => 'Operador de Prueba',
                'email' => 'prueba@mail.com',
                'role_id' => $role->id,
                'password' => '123',
                'password_confirmation' => '123',
            ])
            ->assertSessionHasErrors([
                'password' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);
    }
}
