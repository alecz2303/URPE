<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected_with_generic_message(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Las credenciales proporcionadas no son válidas.',
            ]);

        $this->assertGuest();
    }

    public function test_unknown_account_uses_same_generic_credential_error(): void
    {
        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'no-existe@example.test',
            'password' => 'incorrect-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Las credenciales proporcionadas no son válidas.',
            ]);

        $this->assertGuest();
    }

    public function test_inactive_account_uses_same_generic_credential_error(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
            'is_active' => false,
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Las credenciales proporcionadas no son válidas.',
            ]);

        $this->assertGuest();
    }

    public function test_deactivated_authenticated_session_is_invalidated_on_next_request(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        $user->update(['is_active' => false]);

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($user->name);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
