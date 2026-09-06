<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Therapist;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapistAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_guest_cannot_access_therapist_administration(): void
    {
        $this->get(route('therapists.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_therapist_administration(): void
    {
        $user = User::factory()->create();
        $user->assignRole('therapist');

        $this->actingAs($user)
            ->get(route('therapists.index'))
            ->assertForbidden();
    }

    public function test_user_with_granular_permission_can_view_therapist_administration(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        Therapist::query()->create(['name' => 'Terapeuta de Prueba']);

        $this->actingAs($actor)
            ->get(route('therapists.index'))
            ->assertOk()
            ->assertSee('Terapeuta de Prueba')
            ->assertSee('sweetalert2@11', false);
    }

    public function test_authorized_user_can_create_therapist_with_weekly_schedule_and_audit(): void
    {
        $actor = $this->userWithPermission('therapists.manage');

        $response = $this->actingAs($actor)->post(route('therapists.store'), [
            'name' => 'María Terapeuta',
            'professional_title' => 'Fisioterapeuta',
            'email' => 'maria@urpe.test',
            'is_active' => 1,
            'schedule' => [
                1 => [
                    ['starts_at' => '09:00', 'ends_at' => '13:00'],
                    ['starts_at' => '14:00', 'ends_at' => '18:00'],
                ],
            ],
        ]);

        $therapist = Therapist::query()->where('email', 'maria@urpe.test')->firstOrFail();

        $response
            ->assertRedirect(route('therapists.edit', $therapist))
            ->assertSessionHas('status', 'Terapeuta creado correctamente.');

        $this->assertDatabaseHas('therapist_availability_windows', [
            'therapist_id' => $therapist->id,
            'day_of_week' => 1,
            'starts_at' => '09:00:00',
            'ends_at' => '13:00:00',
        ]);

        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapist.created',
        ]);
        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapist.availability_updated',
        ]);
    }

    public function test_schedule_outside_center_hours_is_rejected_without_creating_therapist(): void
    {
        $actor = $this->userWithPermission('therapists.manage');

        $this->actingAs($actor)
            ->from(route('therapists.create'))
            ->post(route('therapists.store'), [
                'name' => 'Fuera de horario',
                'is_active' => 1,
                'schedule' => [
                    1 => [['starts_at' => '08:00', 'ends_at' => '10:00']],
                ],
            ])
            ->assertRedirect(route('therapists.create'))
            ->assertSessionHasErrors('availability');

        $this->assertDatabaseMissing('therapists', ['name' => 'Fuera de horario']);
        $this->assertSame(0, AuditEvent::query()->where('event', 'therapist.created')->count());
    }

    public function test_authorized_user_can_update_profile_and_schedule(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        $therapist = Therapist::query()->create(['name' => 'Original']);

        $this->actingAs($actor)->put(route('therapists.update', $therapist), [
            'name' => 'Actualizado',
            'professional_title' => 'Terapeuta físico',
            'is_active' => 1,
            'schedule' => [
                2 => [['starts_at' => '10:00', 'ends_at' => '16:00']],
            ],
        ])->assertRedirect(route('therapists.edit', $therapist));

        $this->assertDatabaseHas('therapists', [
            'id' => $therapist->id,
            'name' => 'Actualizado',
        ]);
        $this->assertDatabaseHas('therapist_availability_windows', [
            'therapist_id' => $therapist->id,
            'day_of_week' => 2,
            'starts_at' => '10:00:00',
            'ends_at' => '16:00:00',
        ]);
        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapist.updated',
        ]);
    }

    public function test_authorized_user_can_register_block_from_ui(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        $therapist = Therapist::query()->create(['name' => 'Bloqueable']);

        $this->actingAs($actor)->post(route('therapists.blocks.store', $therapist), [
            'starts_at' => '2026-09-07 12:00:00',
            'ends_at' => '2026-09-07 13:00:00',
            'reason' => 'Reunión clínica',
        ])->assertRedirect(route('therapists.edit', $therapist));

        $this->assertDatabaseHas('therapist_blocks', [
            'therapist_id' => $therapist->id,
            'reason' => 'Reunión clínica',
        ]);
        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapist.block_created',
        ]);
    }

    public function test_dashboard_only_shows_therapist_management_with_permission(): void
    {
        $authorized = $this->userWithPermission('therapists.manage');
        $unauthorized = User::factory()->create();

        $this->actingAs($authorized)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('therapists.index'), false);

        $this->actingAs($unauthorized)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('therapists.index'), false);
    }

    private function userWithPermission(string $permission): User
    {
        $role = Role::query()->create([
            'name' => 'Rol terapeuta admin de prueba',
            'slug' => 'therapist-admin-'.substr(sha1(uniqid('', true)), 0, 12),
            'is_system' => false,
        ]);

        $role->permissions()->sync([
            Permission::query()->where('slug', $permission)->firstOrFail()->id,
        ]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}
