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
}
