<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Patient;
use App\Models\Role;
use App\Services\PatientFolioGenerator;
use App\Services\PatientGuardianManager;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PatientGuardianDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_gets_sequential_annual_folio_and_casts_baseline_attributes(): void
    {
        $first = Patient::query()->create([
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'date_of_birth' => '2020-05-14',
        ]);

        $second = Patient::query()->create([
            'first_name' => 'Luis',
            'last_name' => 'Pérez',
            'date_of_birth' => '2021-06-10',
        ]);

        $year = now()->format('Y');

        $this->assertSame("URPE-{$year}-000001", $first->folio);
        $this->assertSame("URPE-{$year}-000002", $second->folio);
        $this->assertSame('2020-05-14', $first->date_of_birth->format('Y-m-d'));
        $this->assertTrue($first->is_active);
        $this->assertSame('Ana Pérez', $first->full_name);
    }

    public function test_patient_folio_sequence_is_independent_per_year(): void
    {
        $generator = app(PatientFolioGenerator::class);

        $this->assertSame('URPE-2026-000001', $generator->next(2026));
        $this->assertSame('URPE-2026-000002', $generator->next(2026));
        $this->assertSame('URPE-2027-000001', $generator->next(2027));
    }

    public function test_patient_can_have_multiple_guardians_with_relationship_data(): void
    {
        $patient = $this->patient();
        $mother = $this->guardian('María', 'López');
        $father = $this->guardian('José', 'Pérez');
        $manager = app(PatientGuardianManager::class);

        $manager->link($patient, $mother, 'Madre', true);
        $manager->link($patient, $father, 'Padre');

        $this->assertCount(2, $patient->fresh()->guardians);
        $this->assertDatabaseHas('guardian_patient', [
            'patient_id' => $patient->id,
            'guardian_id' => $mother->id,
            'relationship' => 'Madre',
            'is_primary' => true,
        ]);
        $this->assertDatabaseHas('guardian_patient', [
            'patient_id' => $patient->id,
            'guardian_id' => $father->id,
            'relationship' => 'Padre',
            'is_primary' => false,
        ]);
    }

    public function test_linking_a_new_primary_guardian_clears_previous_primary(): void
    {
        $patient = $this->patient();
        $first = $this->guardian('María', 'López');
        $second = $this->guardian('José', 'Pérez');
        $manager = app(PatientGuardianManager::class);

        $manager->link($patient, $first, 'Madre', true);
        $manager->link($patient, $second, 'Padre', true);

        $this->assertDatabaseHas('guardian_patient', [
            'patient_id' => $patient->id,
            'guardian_id' => $first->id,
            'is_primary' => false,
        ]);
        $this->assertDatabaseHas('guardian_patient', [
            'patient_id' => $patient->id,
            'guardian_id' => $second->id,
            'is_primary' => true,
        ]);
        $this->assertSame(1, $patient->guardians()->wherePivot('is_primary', true)->count());
    }

    public function test_primary_guardian_must_already_be_linked_to_patient(): void
    {
        $patient = $this->patient();
        $guardian = $this->guardian('María', 'López');

        $this->expectException(ValidationException::class);

        app(PatientGuardianManager::class)->setPrimary($patient, $guardian);
    }

    public function test_patient_permissions_follow_administrative_baseline_without_global_therapist_access(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $administrator = Role::query()->where('slug', 'administrator')->firstOrFail();
        $coordination = Role::query()->where('slug', 'clinical_coordination')->firstOrFail();
        $reception = Role::query()->where('slug', 'reception')->firstOrFail();
        $direction = Role::query()->where('slug', 'consultation_direction')->firstOrFail();
        $therapist = Role::query()->where('slug', 'therapist')->firstOrFail();

        $this->assertTrue($administrator->permissions()->where('slug', 'patients.view')->exists());
        $this->assertTrue($administrator->permissions()->where('slug', 'patients.manage')->exists());
        $this->assertTrue($coordination->permissions()->where('slug', 'patients.view')->exists());
        $this->assertTrue($coordination->permissions()->where('slug', 'patients.manage')->exists());
        $this->assertTrue($reception->permissions()->where('slug', 'patients.view')->exists());
        $this->assertTrue($reception->permissions()->where('slug', 'patients.manage')->exists());
        $this->assertTrue($direction->permissions()->where('slug', 'patients.view')->exists());
        $this->assertFalse($direction->permissions()->where('slug', 'patients.manage')->exists());
        $this->assertFalse($therapist->permissions()->where('slug', 'patients.view')->exists());
        $this->assertFalse($therapist->permissions()->where('slug', 'patients.manage')->exists());
    }

    private function patient(): Patient
    {
        return Patient::query()->create([
            'first_name' => 'Alan',
            'last_name' => 'Ramírez',
            'date_of_birth' => '2017-02-13',
        ]);
    }

    private function guardian(string $firstName, string $lastName): Guardian
    {
        return Guardian::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => '9610000000',
        ]);
    }
}
