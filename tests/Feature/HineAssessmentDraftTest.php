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
        $patient = Patient::factory()->create();

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

    public function test_user_without_manage_permission_cannot_create_hine_draft(): void
    {
        $user = $this->userWithPermissions(['clinical_assessments.view']);
        $patient = Patient::factory()->create();

        $this->actingAs($user)
            ->post(route('patients.hine-assessments.store', $patient), ['examination_date' => '2026-09-25'])
            ->assertForbidden();

        $this->assertDatabaseCount('clinical_assessments', 0);
    }

    private function userWithPermissions(array $permissionNames): User
    {
        $role = Role::query()->create(['name' => 'hine-'.uniqid(), 'label' => 'HINE Test', 'is_system' => false]);
        $permissions = collect($permissionNames)->map(fn (string $name) => Permission::query()->firstOrCreate(
            ['name' => $name],
            ['label' => $name, 'group' => 'clinical_assessments']
        ));
        $role->permissions()->sync($permissions->pluck('id'));

        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach($role);

        return $user;
    }
}
