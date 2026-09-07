<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalUxPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_shell_exposes_sidebar_and_mobile_menu_controls(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Navegación principal')
            ->assertSee('data-open-mobile-nav', false)
            ->assertSee('data-close-mobile-nav', false)
            ->assertSee('Centro y horarios')
            ->assertSee('Roles y permisos');
    }

    public function test_agenda_modes_render_distinct_presentations(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertSee('Vista diaria');

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'week', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertSee('Sin citas');

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'month', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertSee('Lun')
            ->assertSee('Dom');
    }

    public function test_agenda_exposes_previous_next_today_and_direct_date_navigation(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        $response = $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'week', 'date' => '2026-09-07']));

        $response
            ->assertOk()
            ->assertSee('Periodo anterior')
            ->assertSee('Periodo siguiente')
            ->assertSee('Hoy')
            ->assertSee('type="date"', false)
            ->assertSee('name="date"', false)
            ->assertSee('value="2026-09-07"', false)
            ->assertSee('2026-08-31')
            ->assertSee('2026-09-14');
    }
}
