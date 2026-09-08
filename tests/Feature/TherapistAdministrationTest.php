<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Therapist;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_authorized_user_can_create_therapist_with_account_schedule_role_and_audit(): void
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
        $user = User::query()->where('email', 'maria@urpe.test')->firstOrFail();
        $credentials = $response->getSession()->get('therapist_credentials');

        $response
            ->assertRedirect(route('therapists.edit', $therapist))
            ->assertSessionHas('status', 'Terapeuta y acceso al sistema creados correctamente.')
            ->assertSessionHas('therapist_credentials');

        $this->assertSame($user->id, $therapist->user_id);
        $this->assertSame('María Terapeuta', $user->name);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole('therapist'));
        $this->assertSame('maria@urpe.test', $credentials['email']);
        $this->assertTrue(Hash::check($credentials['password'], $user->password));

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

    public function test_create_form_no_longer_exposes_manual_linked_user_selector(): void
    {
        $actor = $this->userWithPermission('therapists.manage');

        $this->actingAs($actor)
            ->get(route('therapists.create'))
            ->assertOk()
            ->assertDontSee('Usuario vinculado')
            ->assertSee('Correo electrónico / usuario de acceso');
    }

    public function test_duplicate_login_email_is_rejected_without_partial_therapist_creation(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        User::factory()->create(['email' => 'ocupado@urpe.test']);

        $this->actingAs($actor)
            ->from(route('therapists.create'))
            ->post(route('therapists.store'), [
                'name' => 'Cuenta duplicada',
                'email' => 'ocupado@urpe.test',
                'is_active' => 1,
            ])
            ->assertRedirect(route('therapists.create'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('therapists', ['name' => 'Cuenta duplicada']);
        $this->assertSame(1, User::query()->where('email', 'ocupado@urpe.test')->count());
    }

    public function test_schedule_outside_center_hours_is_rejected_without_creating_therapist_or_user(): void
    {
        $actor = $this->userWithPermission('therapists.manage');

        $this->actingAs($actor)
            ->from(route('therapists.create'))
            ->post(route('therapists.store'), [
                'name' => 'Fuera de horario',
                'email' => 'fuera@urpe.test',
                'is_active' => 1,
                'schedule' => [
                    1 => [['starts_at' => '08:00', 'ends_at' => '10:00']],
                ],
            ])
            ->assertRedirect(route('therapists.create'))
            ->assertSessionHasErrors('availability');

        $this->assertDatabaseMissing('therapists', ['name' => 'Fuera de horario']);
        $this->assertDatabaseMissing('users', ['email' => 'fuera@urpe.test']);
        $this->assertSame(0, AuditEvent::query()->where('event', 'therapist.created')->count());
    }

    public function test_authorized_user_can_update_profile_schedule_and_linked_access_together(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        $user = User::factory()->create([
            'name' => 'Original',
            'email' => 'original@urpe.test',
            'is_active' => true,
        ]);
        $user->assignRole('therapist');
        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => 'Original',
            'email' => 'original@urpe.test',
            'is_active' => true,
        ]);

        $this->actingAs($actor)->put(route('therapists.update', $therapist), [
            'name' => 'Actualizado',
            'professional_title' => 'Terapeuta físico',
            'email' => 'actualizado@urpe.test',
            'is_active' => 1,
            'schedule' => [
                2 => [['starts_at' => '10:00', 'ends_at' => '16:00']],
            ],
        ])->assertRedirect(route('therapists.edit', $therapist));

        $this->assertDatabaseHas('therapists', [
            'id' => $therapist->id,
            'name' => 'Actualizado',
            'email' => 'actualizado@urpe.test',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Actualizado',
            'email' => 'actualizado@urpe.test',
            'is_active' => 1,
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

    public function test_deactivating_therapist_also_disables_login_without_deleting_history(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        $user = User::factory()->create([
            'email' => 'salida@urpe.test',
            'is_active' => true,
        ]);
        $user->assignRole('therapist');
        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => 'Terapeuta Salida',
            'email' => 'salida@urpe.test',
            'is_active' => true,
        ]);

        $this->actingAs($actor)->put(route('therapists.update', $therapist), [
            'name' => 'Terapeuta Salida',
            'email' => 'salida@urpe.test',
            'is_active' => 0,
        ])->assertRedirect(route('therapists.edit', $therapist));

        $this->assertDatabaseHas('therapists', [
            'id' => $therapist->id,
            'is_active' => 0,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => 0,
        ]);
    }

    public function test_legacy_unlinked_therapist_gets_account_when_profile_is_saved(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        $therapist = Therapist::query()->create([
            'name' => 'Perfil legado',
            'email' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($actor)->put(route('therapists.update', $therapist), [
            'name' => 'Perfil legado',
            'email' => 'legado@urpe.test',
            'is_active' => 1,
        ]);

        $therapist->refresh();
        $user = User::query()->where('email', 'legado@urpe.test')->firstOrFail();

        $response->assertSessionHas('therapist_credentials');
        $this->assertSame($user->id, $therapist->user_id);
        $this->assertTrue($user->hasRole('therapist'));
    }

    public function test_authorized_user_can_register_block_from_ui(): void
    {
        $actor = $this->userWithPermission('therapists.manage');
        $therapist = Therapist::query()->create(['name' => 'Bloqueable']);

        $this->actingAs($actor)->post(route('therapists.blocks.store', $therapist), [
            'starts_at' => '2026-09-07 12:00:00',
            'ends_at' => '2026-09-07 13:00:00',
            'reason' => 'Reunión clínica',
        ])->assertRedirect(route('therapists.blocks.index', $therapist));

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
