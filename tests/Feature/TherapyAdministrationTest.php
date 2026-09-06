<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TherapyAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_guest_cannot_access_therapy_administration(): void
    {
        $this->get(route('therapies.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_therapy_administration(): void
    {
        $user = User::factory()->create();
        $user->assignRole('therapist');

        $this->actingAs($user)
            ->get(route('therapies.index'))
            ->assertForbidden();
    }

    public function test_user_with_granular_permission_can_view_therapy_administration(): void
    {
        $actor = $this->userWithPermission('therapies.manage');
        Therapy::query()->create([
            'name' => 'Terapia Visible',
            'duration_minutes' => 30,
            'required_therapists' => 1,
            'color' => '#123ABC',
        ]);

        $this->actingAs($actor)
            ->get(route('therapies.index'))
            ->assertOk()
            ->assertSee('Terapia Visible')
            ->assertSee('sweetalert2@11', false);
    }

    public function test_authorized_user_can_create_therapy_and_audit_it(): void
    {
        $actor = $this->userWithPermission('therapies.manage');

        $response = $this->actingAs($actor)->post(route('therapies.store'), [
            'name' => 'Integración Sensorial',
            'duration_minutes' => 50,
            'required_therapists' => 2,
            'color' => '#12AB34',
            'is_active' => 1,
        ]);

        $therapy = Therapy::query()->where('name', 'Integración Sensorial')->firstOrFail();

        $response
            ->assertRedirect(route('therapies.edit', $therapy))
            ->assertSessionHas('status', 'Terapia creada correctamente.');

        $this->assertDatabaseHas('therapies', [
            'id' => $therapy->id,
            'duration_minutes' => 50,
            'required_therapists' => 2,
            'color' => '#12AB34',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapy.created',
            'target_id' => (string) $therapy->id,
        ]);
    }

    public function test_invalid_duration_therapist_count_and_color_are_rejected(): void
    {
        $actor = $this->userWithPermission('therapies.manage');

        $this->actingAs($actor)
            ->from(route('therapies.create'))
            ->post(route('therapies.store'), [
                'name' => 'Inválida',
                'duration_minutes' => 0,
                'required_therapists' => 0,
                'color' => 'azul',
                'is_active' => 1,
            ])
            ->assertRedirect(route('therapies.create'))
            ->assertSessionHasErrors(['duration_minutes', 'required_therapists', 'color']);

        $this->assertDatabaseMissing('therapies', ['name' => 'Inválida']);
    }

    public function test_authorized_user_can_update_therapy_and_audit_changes(): void
    {
        $actor = $this->userWithPermission('therapies.manage');
        $therapy = Therapy::query()->create([
            'name' => 'Original',
            'duration_minutes' => 30,
            'required_therapists' => 1,
            'color' => '#111111',
        ]);

        $this->actingAs($actor)->put(route('therapies.update', $therapy), [
            'name' => 'Actualizada',
            'duration_minutes' => 75,
            'required_therapists' => 3,
            'color' => '#ABCDEF',
            'is_active' => 1,
        ])->assertRedirect(route('therapies.edit', $therapy));

        $this->assertDatabaseHas('therapies', [
            'id' => $therapy->id,
            'name' => 'Actualizada',
            'duration_minutes' => 75,
            'required_therapists' => 3,
            'color' => '#ABCDEF',
        ]);

        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapy.updated',
            'target_id' => (string) $therapy->id,
        ]);
    }

    public function test_authorized_user_can_deactivate_therapy_without_deleting_it(): void
    {
        $actor = $this->userWithPermission('therapies.manage');
        $therapy = Therapy::query()->create([
            'name' => 'Con historial',
            'duration_minutes' => 45,
            'required_therapists' => 1,
            'color' => '#445566',
            'is_active' => true,
        ]);

        $this->actingAs($actor)
            ->patch(route('therapies.toggle-active', $therapy))
            ->assertRedirect(route('therapies.index'))
            ->assertSessionHas('status', 'Terapia desactivada correctamente.');

        $this->assertDatabaseHas('therapies', [
            'id' => $therapy->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapy.status_updated',
            'target_id' => (string) $therapy->id,
        ]);
    }

    public function test_duplicate_therapy_name_is_rejected(): void
    {
        $actor = $this->userWithPermission('therapies.manage');
        Therapy::query()->create([
            'name' => 'Vojta',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3B82F6',
        ]);

        $this->actingAs($actor)->post(route('therapies.store'), [
            'name' => 'Vojta',
            'duration_minutes' => 50,
            'required_therapists' => 2,
            'color' => '#000000',
            'is_active' => 1,
        ])->assertSessionHasErrors('name');
    }

    public function test_dashboard_only_shows_therapy_management_with_permission(): void
    {
        $authorized = $this->userWithPermission('therapies.manage');
        $unauthorized = User::factory()->create();

        $this->actingAs($authorized)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('therapies.index'), false);

        $this->actingAs($unauthorized)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('therapies.index'), false);
    }

    private function userWithPermission(string $permission): User
    {
        $role = Role::query()->create([
            'name' => 'Rol terapias de prueba',
            'slug' => 'therapy-admin-'.substr(sha1(uniqid('', true)), 0, 12),
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
