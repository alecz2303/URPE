<?php

namespace Tests\Feature;

use App\Models\ClinicalAssessment;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HineAssessmentDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_hine_draft_for_patient(): void
    {
        $user = $this->userWithPermissions(['clinical_assessments.view', 'clinical_assessments.manage']);
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'HINE',
            'date_of_birth' => '2026-03-25',
        ]);

        $response = $this->actingAs($user)->post(route('patients.hine-assessments.store', $patient), [
            'examination_date' => '2026-09-25',
            'gestational_age' => '38',
            'chronological_age' => '6 meses',
            'corrected_age' => '5 meses',
            'head_circumference' => '42',
        ]);

        $assessment = ClinicalAssessment::query()->where('patient_id', $patient->id)->firstOrFail();

        $response->assertRedirect(route('patients.hine-assessments.index', $patient));
        $this->assertSame(ClinicalAssessment::TYPE_HINE, $assessment->type);
        $this->assertSame(ClinicalAssessment::STATUS_DRAFT, $assessment->status);
        $this->assertSame('v ES 08.12.18', $assessment->instrument_version);
        $this->assertSame('6 meses', $assessment->hine->chronological_age);
        $this->assertDatabaseHas('audit_events', [
            'event' => 'clinical_assessment.created',
            'target_id' => (string) $assessment->id,
        ]);
    }

    public function test_authorized_user_can_capture_half_point_neurological_scores_and_explicit_asymmetry(): void
    {
        $user = $this->userWithPermissions(['clinical_assessments.view', 'clinical_assessments.manage']);
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Captura HINE',
            'date_of_birth' => '2026-03-25',
        ]);

        $this->actingAs($user)->post(route('patients.hine-assessments.store', $patient), [
            'examination_date' => '2026-09-25',
        ]);

        $assessment = ClinicalAssessment::query()->where('patient_id', $patient->id)->firstOrFail();

        $this->actingAs($user)->put(route('patients.hine-assessments.update', [$patient, $assessment]), [
            'responses' => [
                'facial_appearance' => ['score' => 2.5, 'comments' => 'Observación clínica'],
                'scarf_sign' => ['score' => 1.5, 'asymmetry' => 1],
            ],
        ])->assertRedirect(route('patients.hine-assessments.edit', [$patient, $assessment]));

        $assessment->refresh()->load('hine.responses');

        $this->assertSame('4.0', $assessment->hine->global_score);
        $this->assertSame(1, $assessment->hine->asymmetry_count);
        $this->assertDatabaseHas('hine_responses', [
            'hine_assessment_id' => $assessment->hine->id,
            'item_key' => 'facial_appearance',
            'score' => 2.5,
        ]);
        $this->assertDatabaseHas('audit_events', [
            'event' => 'clinical_assessment.draft_updated',
            'target_id' => (string) $assessment->id,
        ]);
    }

    public function test_motor_milestones_and_behavior_are_persisted_without_changing_global_score(): void
    {
        $user = $this->userWithPermissions(['clinical_assessments.view', 'clinical_assessments.manage']);
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Hitos HINE',
            'date_of_birth' => '2026-03-25',
        ]);

        $this->actingAs($user)->post(route('patients.hine-assessments.store', $patient), [
            'examination_date' => '2026-09-25',
        ]);

        $assessment = ClinicalAssessment::query()->where('patient_id', $patient->id)->firstOrFail();

        $this->actingAs($user)->put(route('patients.hine-assessments.update', [$patient, $assessment]), [
            'responses' => ['facial_appearance' => ['score' => 3]],
            'motor' => [
                'head_control' => ['observed' => 'Observado', 'acquisition_age' => '3 meses'],
            ],
            'behavior' => [
                'consciousness' => ['option' => 5],
            ],
        ])->assertRedirect();

        $assessment->refresh()->load('hine.responses');

        $this->assertSame('3.0', $assessment->hine->global_score);
        $this->assertSame('Observado', $assessment->hine->responses->firstWhere('item_key', 'head_control')->response_data['observed']);
        $this->assertSame(5, $assessment->hine->responses->firstWhere('item_key', 'consciousness')->response_data['option']);
        $this->assertNull($assessment->hine->responses->firstWhere('item_key', 'head_control')->score);
        $this->assertNull($assessment->hine->responses->firstWhere('item_key', 'consciousness')->score);
    }

    public function test_user_without_manage_permission_cannot_create_hine_draft(): void
    {
        $user = $this->userWithPermissions(['clinical_assessments.view']);
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'HINE',
            'date_of_birth' => '2026-03-25',
        ]);

        $this->actingAs($user)
            ->post(route('patients.hine-assessments.store', $patient), ['examination_date' => '2026-09-25'])
            ->assertForbidden();

        $this->assertDatabaseCount('clinical_assessments', 0);
    }

    private function userWithPermissions(array $permissionNames): User
    {
        $slug = 'hine-'.uniqid();
        $role = Role::query()->create(['name' => 'HINE Test', 'slug' => $slug, 'is_system' => false]);
        $permissions = collect($permissionNames)->map(fn (string $name) => Permission::query()->firstOrCreate(
            ['slug' => $name],
            ['name' => $name, 'description' => 'HINE test permission']
        ));
        $role->permissions()->sync($permissions->pluck('id'));

        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach($role);

        return $user;
    }
}
