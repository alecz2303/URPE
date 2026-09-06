<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientAdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_guest_cannot_access_patient_administration(): void
    {
        $this->get(route('patients.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_patient_permission_is_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('therapist');

        $this->actingAs($user)->get(route('patients.index'))->assertForbidden();
    }

    public function test_read_only_permission_can_view_but_not_create_patients(): void
    {
        $actor = $this->userWithPermissions(['patients.view']);
        $patient = Patient::query()->create($this->patientData());

        $this->actingAs($actor)
            ->get(route('patients.show', $patient))
            ->assertOk()
            ->assertSee($patient->folio)
            ->assertSee('sweetalert2@11', false);

        $this->actingAs($actor)->get(route('patients.create'))->assertForbidden();
    }

    public function test_authorized_user_can_create_patient_with_generated_folio_and_audit(): void
    {
        $actor = $this->userWithPermissions(['patients.view', 'patients.manage']);

        $response = $this->actingAs($actor)->post(route('patients.store'), $this->patientData());
        $patient = Patient::query()->firstOrFail();

        $response->assertRedirect(route('patients.show', $patient));
        $this->assertStringStartsWith('URPE-', $patient->folio);
        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'patient.created',
            'target_id' => (string) $patient->id,
        ]);
    }

    public function test_invalid_patient_data_is_rejected_without_persistence(): void
    {
        $actor = $this->userWithPermissions(['patients.manage']);

        $this->actingAs($actor)->post(route('patients.store'), [
            'first_name' => '',
            'last_name' => '',
            'date_of_birth' => now()->addDay()->format('Y-m-d'),
            'email' => 'correo-invalido',
            'is_active' => 1,
        ])->assertSessionHasErrors(['first_name', 'last_name', 'date_of_birth', 'email']);

        $this->assertDatabaseCount('patients', 0);
    }

    public function test_authorized_user_can_update_and_deactivate_patient_without_deleting_it(): void
    {
        $actor = $this->userWithPermissions(['patients.manage']);
        $patient = Patient::query()->create($this->patientData());

        $this->actingAs($actor)->put(route('patients.update', $patient), array_merge($this->patientData(), [
            'first_name' => 'Alan',
        ]))->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'first_name' => 'Alan']);
        $this->assertDatabaseHas('audit_events', ['event' => 'patient.updated', 'target_id' => (string) $patient->id]);

        $this->actingAs($actor)->patch(route('patients.toggle-active', $patient))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'is_active' => false]);
        $this->assertDatabaseHas('audit_events', ['event' => 'patient.status_updated', 'target_id' => (string) $patient->id]);
    }

    public function test_guardian_creation_is_atomic_and_audited(): void
    {
        $actor = $this->userWithPermissions(['patients.manage']);
        $patient = Patient::query()->create($this->patientData());

        $this->actingAs($actor)->post(route('patients.guardians.store', $patient), [
            'first_name' => 'Karelly',
            'last_name' => 'Ramírez',
            'phone' => '9610000000',
            'email' => 'responsable@example.test',
            'relationship' => 'Madre',
            'is_primary' => 1,
        ])->assertRedirect(route('patients.show', $patient));

        $guardian = Guardian::query()->firstOrFail();
        $this->assertDatabaseHas('guardian_patient', [
            'patient_id' => $patient->id,
            'guardian_id' => $guardian->id,
            'relationship' => 'Madre',
            'is_primary' => true,
        ]);
        $this->assertDatabaseHas('audit_events', [
            'event' => 'guardian.created',
            'target_id' => (string) $guardian->id,
        ]);

        $this->actingAs($actor)->post(route('patients.guardians.store', $patient), [
            'first_name' => 'Sin',
            'last_name' => 'Teléfono',
            'phone' => '',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('guardians', 1);
    }

    public function test_setting_primary_guardian_keeps_single_primary_contact(): void
    {
        $actor = $this->userWithPermissions(['patients.manage']);
        $patient = Patient::query()->create($this->patientData());
        $first = Guardian::query()->create(['first_name' => 'Uno', 'last_name' => 'Responsable', 'phone' => '111']);
        $second = Guardian::query()->create(['first_name' => 'Dos', 'last_name' => 'Responsable', 'phone' => '222']);
        $patient->guardians()->attach($first->id, ['relationship' => 'Madre', 'is_primary' => true]);
        $patient->guardians()->attach($second->id, ['relationship' => 'Padre', 'is_primary' => false]);

        $this->actingAs($actor)
            ->patch(route('patients.guardians.primary', [$patient, $second]))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('guardian_patient', ['patient_id' => $patient->id, 'guardian_id' => $first->id, 'is_primary' => false]);
        $this->assertDatabaseHas('guardian_patient', ['patient_id' => $patient->id, 'guardian_id' => $second->id, 'is_primary' => true]);
        $this->assertSame(1, $patient->guardians()->wherePivot('is_primary', true)->count());
        $this->assertDatabaseHas('audit_events', ['event' => 'guardian.primary_updated', 'target_id' => (string) $second->id]);
    }

    public function test_dashboard_only_exposes_patient_module_to_users_with_view_permission(): void
    {
        $authorized = $this->userWithPermissions(['patients.view']);
        $unauthorized = User::factory()->create();

        $this->actingAs($authorized)->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('patients.index'), false);

        $this->actingAs($unauthorized)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('patients.index'), false);
    }

    private function patientData(): array
    {
        return [
            'first_name' => 'Paciente',
            'middle_name' => null,
            'last_name' => 'Prueba',
            'second_last_name' => null,
            'date_of_birth' => '2020-05-10',
            'sex' => 'male',
            'phone' => '9611234567',
            'email' => 'paciente@example.test',
            'address_line' => 'Tuxtla Gutiérrez, Chiapas',
            'administrative_notes' => 'Nota administrativa de prueba.',
            'is_active' => 1,
        ];
    }

    private function userWithPermissions(array $permissions): User
    {
        $role = Role::query()->create([
            'name' => 'Rol pacientes de prueba',
            'slug' => 'patient-test-'.substr(sha1(uniqid('', true)), 0, 12),
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            Permission::query()->whereIn('slug', $permissions)->pluck('id')->all(),
        );

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}
