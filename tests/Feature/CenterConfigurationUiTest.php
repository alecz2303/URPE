<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CenterConfigurationUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_center_configuration(): void
    {
        $this->get(route('center.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_center_configuration(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('therapist');

        $this->actingAs($user)
            ->get(route('center.edit'))
            ->assertForbidden();
    }

    public function test_administrator_can_view_center_configuration_with_sweetalert_and_default_hours(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        $this->actingAs($user)
            ->get(route('center.edit'))
            ->assertOk()
            ->assertSee('Configuración del centro')
            ->assertSee('Horario semanal')
            ->assertSee('09:00')
            ->assertSee('18:00')
            ->assertSee('sweetalert2@11', false)
            ->assertSee('data-swal-confirm', false);
    }

    public function test_administrator_can_update_center_settings_and_hours_from_ui(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        $response = $this->actingAs($user)->put(route('center.update'), [
            'name' => 'URPE',
            'phone' => '9611234567',
            'email' => 'contacto@urpe.test',
            'address' => 'Tuxtla Gutiérrez, Chiapas',
            'timezone' => 'America/Mexico_City',
            'hours' => $this->validHours(),
        ]);

        $response
            ->assertRedirect(route('center.edit'))
            ->assertSessionHas('status', 'Configuración del centro actualizada correctamente.');

        $this->assertDatabaseHas('center_settings', [
            'name' => 'URPE',
            'phone' => '9611234567',
            'email' => 'contacto@urpe.test',
        ]);

        $this->assertDatabaseHas('center_operating_hours', [
            'day_of_week' => 1,
            'is_enabled' => true,
            'opens_at' => '08:30:00',
            'closes_at' => '17:30:00',
        ]);

        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $user->id,
            'event' => 'center.configuration_updated',
        ]);
    }

    public function test_invalid_email_is_rejected_with_validation_feedback(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        $this->actingAs($user)
            ->from(route('center.edit'))
            ->put(route('center.update'), [
                'name' => 'URPE',
                'email' => 'correo-invalido',
                'timezone' => 'America/Mexico_City',
                'hours' => $this->validHours(),
            ])
            ->assertRedirect(route('center.edit'))
            ->assertSessionHasErrors('email');
    }

    public function test_overlapping_windows_are_rejected_from_ui(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrator');

        $hours = $this->validHours();
        $hours[1] = [
            ['is_enabled' => 1, 'opens_at' => '09:00', 'closes_at' => '13:00'],
            ['is_enabled' => 1, 'opens_at' => '12:00', 'closes_at' => '18:00'],
        ];

        $this->actingAs($user)
            ->from(route('center.edit'))
            ->put(route('center.update'), [
                'name' => 'URPE',
                'timezone' => 'America/Mexico_City',
                'hours' => $hours,
            ])
            ->assertRedirect(route('center.edit'))
            ->assertSessionHasErrors('hours');

        $this->assertSame(0, AuditEvent::query()->where('event', 'center.configuration_updated')->count());
    }

    public function test_dashboard_shows_center_configuration_only_with_permission(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $therapist = User::factory()->create();
        $therapist->assignRole('therapist');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('center.edit'), false);

        $this->actingAs($therapist)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('center.edit'), false);
    }

    private function validHours(): array
    {
        return [
            1 => [['is_enabled' => 1, 'opens_at' => '08:30', 'closes_at' => '17:30']],
            2 => [['is_enabled' => 1, 'opens_at' => '09:00', 'closes_at' => '18:00']],
            3 => [['is_enabled' => 1, 'opens_at' => '09:00', 'closes_at' => '18:00']],
            4 => [['is_enabled' => 1, 'opens_at' => '09:00', 'closes_at' => '18:00']],
            5 => [['is_enabled' => 1, 'opens_at' => '09:00', 'closes_at' => '18:00']],
            6 => [['is_enabled' => 0, 'opens_at' => null, 'closes_at' => null]],
            7 => [['is_enabled' => 0, 'opens_at' => null, 'closes_at' => null]],
        ];
    }
}
