<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalRecordAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_clinical_record(): void
    {
        $patient = $this->patient();

        $this->get(route('clinical-records.show', $patient))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_clinical_permission_is_forbidden(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole('therapist');

        $this->actingAs($user)
            ->get(route('clinical-records.show', $patient))
            ->assertForbidden();
    }

    public function test_read_only_user_can_view_record_but_cannot_update_it(): void
    {
        $permission = Permission::query()->create([
            'name' => 'Ver expediente clínico',
            'slug' => 'clinical_records.view',
        ]);
        $role = Role::query()->create([
            'name' => 'Lectura clínica',
            'slug' => 'clinical_reader',
        ]);
        $role->permissions()->attach($permission);

        $patient = $this->patient();
        $patient->clinicalRecord()->create([
            'diagnoses' => 'Diagnóstico de prueba',
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('clinical-records.show', $patient))
            ->assertOk()
            ->assertSee('Diagnóstico de prueba')
            ->assertDontSee('Guardar cambios');

        $this->actingAs($user)
            ->put(route('clinical-records.update', $patient), [
                'diagnoses' => 'Intento no autorizado',
            ])
            ->assertForbidden();
    }

    public function test_authorized_user_can_create_clinical_record_and_audit_without_clinical_payload(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole('clinical_coordination');

        $payload = [
            'medical_history' => 'Antecedente médico sensible.',
            'prenatal_perinatal_history' => 'Dato perinatal sensible.',
            'developmental_history' => 'Dato de desarrollo sensible.',
            'family_history' => 'Dato familiar sensible.',
            'diagnoses' => 'Diagnóstico clínico sensible.',
            'therapeutic_objectives' => 'Objetivo terapéutico sensible.',
            'general_observations' => 'Observación clínica sensible.',
        ];

        $this->actingAs($user)
            ->put(route('clinical-records.update', $patient), $payload)
            ->assertRedirect(route('clinical-records.show', $patient))
            ->assertSessionHas('status', 'Expediente clínico creado correctamente.');

        $this->assertDatabaseHas('clinical_records', [
            'patient_id' => $patient->id,
            'diagnoses' => 'Diagnóstico clínico sensible.',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $event = AuditEvent::query()->where('event', 'clinical_record.created')->firstOrFail();
        $metadata = json_encode($event->metadata, JSON_UNESCAPED_UNICODE);

        $this->assertStringContainsString('patient_folio', $metadata);
        $this->assertStringContainsString('sections_with_content', $metadata);
        foreach ($payload as $sensitiveValue) {
            $this->assertStringNotContainsString($sensitiveValue, $metadata);
        }
    }

    public function test_authorized_user_can_update_existing_record_without_creating_duplicate(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole('clinical_coordination');

        $record = $patient->clinicalRecord()->create([
            'medical_history' => 'Anterior',
            'diagnoses' => 'Diagnóstico previo',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->put(route('clinical-records.update', $patient), [
                'medical_history' => 'Actualizado',
                'diagnoses' => 'Diagnóstico nuevo',
            ])
            ->assertRedirect(route('clinical-records.show', $patient))
            ->assertSessionHas('status', 'Expediente clínico actualizado correctamente.');

        $this->assertDatabaseCount('clinical_records', 1);
        $this->assertDatabaseHas('clinical_records', [
            'id' => $record->id,
            'medical_history' => 'Actualizado',
            'diagnoses' => 'Diagnóstico nuevo',
            'updated_by' => $user->id,
        ]);

        $event = AuditEvent::query()->where('event', 'clinical_record.updated')->firstOrFail();
        $this->assertContains('medical_history', $event->metadata['changed_sections']);
        $this->assertContains('diagnoses', $event->metadata['changed_sections']);
        $this->assertStringNotContainsString('Diagnóstico nuevo', json_encode($event->metadata, JSON_UNESCAPED_UNICODE));
    }

    public function test_clinical_record_form_renders_sweetalert_feedback_baseline(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole('clinical_coordination');

        $this->actingAs($user)
            ->withSession(['status' => 'Expediente clínico actualizado correctamente.'])
            ->get(route('clinical-records.show', $patient))
            ->assertOk()
            ->assertSee('sweetalert2', false)
            ->assertSee('Expediente clínico base');
    }

    public function test_clinical_record_rejects_oversized_section_without_persistence(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole('clinical_coordination');

        $this->actingAs($user)
            ->from(route('clinical-records.show', $patient))
            ->put(route('clinical-records.update', $patient), [
                'diagnoses' => str_repeat('x', 10001),
            ])
            ->assertRedirect(route('clinical-records.show', $patient))
            ->assertSessionHasErrors('diagnoses');

        $this->assertDatabaseCount('clinical_records', 0);
    }

    public function test_patient_show_displays_clinical_record_link_when_user_has_clinical_view_permission(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole('clinical_coordination');

        $this->actingAs($user)
            ->get(route('patients.show', $patient))
            ->assertOk()
            ->assertSee('Expediente clínico')
            ->assertSee(route('clinical-records.show', $patient), false);
    }

    public function test_patient_show_hides_clinical_record_link_without_clinical_view_permission(): void
    {
        $patientsView = Permission::query()->create([
            'name' => 'Ver pacientes',
            'slug' => 'patients.view',
        ]);
        $role = Role::query()->create([
            'name' => 'Consulta administrativa',
            'slug' => 'administrative_reader',
        ]);
        $role->permissions()->attach($patientsView);

        $patient = $this->patient();
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('patients.show', $patient))
            ->assertOk()
            ->assertDontSee(route('clinical-records.show', $patient), false);
    }

    private function patient(): Patient
    {
        return Patient::query()->create([
            'first_name' => 'Alan',
            'last_name' => 'Ramírez',
            'date_of_birth' => '2017-02-13',
        ]);
    }
}
