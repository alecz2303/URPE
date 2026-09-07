<?php

namespace Tests\Feature;

use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalRecordDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_has_at_most_one_base_clinical_record(): void
    {
        $patient = $this->patient();

        $record = ClinicalRecord::query()->create([
            'patient_id' => $patient->id,
            'medical_history' => 'Antecedente clínico relevante.',
            'diagnoses' => 'Diagnóstico de referencia.',
            'therapeutic_objectives' => 'Objetivo terapéutico inicial.',
        ]);

        $this->assertTrue($patient->fresh()->clinicalRecord->is($record));
        $this->assertSame('Antecedente clínico relevante.', $record->medical_history);

        $this->expectException(QueryException::class);

        ClinicalRecord::query()->create([
            'patient_id' => $patient->id,
            'general_observations' => 'Segundo expediente no permitido.',
        ]);
    }

    public function test_clinical_record_keeps_structured_baseline_sections_separate_from_patient_administration(): void
    {
        $patient = $this->patient();

        $record = ClinicalRecord::query()->create([
            'patient_id' => $patient->id,
            'medical_history' => 'Cirugías y tratamientos previos.',
            'prenatal_perinatal_history' => 'Antecedentes prenatales y perinatales.',
            'developmental_history' => 'Hitos del desarrollo.',
            'family_history' => 'Antecedentes familiares.',
            'diagnoses' => 'Diagnósticos de referencia.',
            'therapeutic_objectives' => 'Objetivos generales del proceso.',
            'general_observations' => 'Observaciones clínicas generales.',
        ]);

        $this->assertDatabaseHas('clinical_records', [
            'id' => $record->id,
            'patient_id' => $patient->id,
        ]);
        $this->assertSame('Observación administrativa', $patient->administrative_notes);
        $this->assertSame('Diagnósticos de referencia.', $record->diagnoses);
        $this->assertSame('Objetivos generales del proceso.', $record->therapeutic_objectives);
    }

    public function test_clinical_record_tracks_creator_and_last_updater_when_available(): void
    {
        $creator = User::factory()->create();
        $updater = User::factory()->create();
        $patient = $this->patient();

        $record = ClinicalRecord::query()->create([
            'patient_id' => $patient->id,
            'created_by' => $creator->id,
            'updated_by' => $creator->id,
        ]);

        $record->update([
            'general_observations' => 'Actualización clínica.',
            'updated_by' => $updater->id,
        ]);

        $this->assertTrue($record->fresh()->creator->is($creator));
        $this->assertTrue($record->fresh()->updater->is($updater));
    }

    public function test_clinical_record_permissions_are_conservative_by_default(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $administrator = Role::query()->where('slug', 'administrator')->firstOrFail();
        $coordination = Role::query()->where('slug', 'clinical_coordination')->firstOrFail();
        $therapist = Role::query()->where('slug', 'therapist')->firstOrFail();
        $reception = Role::query()->where('slug', 'reception')->firstOrFail();
        $direction = Role::query()->where('slug', 'consultation_direction')->firstOrFail();

        $this->assertTrue($administrator->permissions()->where('slug', 'clinical_records.view')->exists());
        $this->assertTrue($administrator->permissions()->where('slug', 'clinical_records.manage')->exists());
        $this->assertTrue($coordination->permissions()->where('slug', 'clinical_records.view')->exists());
        $this->assertTrue($coordination->permissions()->where('slug', 'clinical_records.manage')->exists());

        $this->assertFalse($therapist->permissions()->where('slug', 'clinical_records.view')->exists());
        $this->assertFalse($therapist->permissions()->where('slug', 'clinical_records.manage')->exists());
        $this->assertFalse($reception->permissions()->where('slug', 'clinical_records.view')->exists());
        $this->assertFalse($reception->permissions()->where('slug', 'clinical_records.manage')->exists());
        $this->assertFalse($direction->permissions()->where('slug', 'clinical_records.view')->exists());
        $this->assertFalse($direction->permissions()->where('slug', 'clinical_records.manage')->exists());
    }

    private function patient(): Patient
    {
        return Patient::query()->create([
            'first_name' => 'Alan',
            'last_name' => 'Ramírez',
            'date_of_birth' => '2017-02-13',
            'administrative_notes' => 'Observación administrativa',
        ]);
    }
}
