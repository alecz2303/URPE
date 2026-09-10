<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AuditEvent;
use App\Models\ClinicalSessionLog;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalSessionWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_workspace_renders_autosave_and_recent_clinical_context(): void
    {
        [$user, $therapist] = $this->linkedTherapist();
        [$patient, $therapy] = $this->clinicalEntities();

        $previousAppointment = $this->appointment($patient, $therapy, $therapist, '2026-09-08 10:00:00');
        $previous = ClinicalSessionLog::query()->create([
            'appointment_id' => $previousAppointment->id,
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'authored_by_user_id' => $user->id,
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'treatment_activities' => 'Trabajo previo.',
            'patient_response' => 'Mejor tolerancia y mayor control.',
            'next_session_objectives' => 'Aumentar control de tronco.',
            'completed_at' => now(),
        ]);
        $previous->participatingTherapists()->attach($therapist);

        $appointment = $this->appointment($patient, $therapy, $therapist, '2026-09-09 10:00:00');

        $this->actingAs($user)
            ->get(route('session-logs.edit', $appointment))
            ->assertOk()
            ->assertSee('Contexto longitudinal')
            ->assertSee('Sesiones recientes')
            ->assertSee('Mejor tolerancia y mayor control.')
            ->assertSee('Aumentar control de tronco.')
            ->assertSee(route('session-logs.autosave', $appointment))
            ->assertSee('Guardar y salir')
            ->assertSee('Completar sesión');
    }

    public function test_autosave_creates_and_reuses_one_draft_per_appointment(): void
    {
        [$user, $therapist] = $this->linkedTherapist();
        [$patient, $therapy] = $this->clinicalEntities();
        $appointment = $this->appointment($patient, $therapy, $therapist, '2026-09-09 11:00:00');

        $this->actingAs($user)
            ->postJson(route('session-logs.autosave', $appointment), [
                'participant_ids' => [$therapist->id],
                'treatment_activities' => 'Primer autoguardado.',
                'patient_response' => 'Respuesta inicial.',
            ])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->actingAs($user)
            ->postJson(route('session-logs.autosave', $appointment), [
                'participant_ids' => [$therapist->id],
                'treatment_activities' => 'Segundo autoguardado.',
                'patient_response' => 'Respuesta actualizada.',
            ])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertSame(1, ClinicalSessionLog::query()->where('appointment_id', $appointment->id)->count());
        $this->assertDatabaseHas('clinical_session_logs', [
            'appointment_id' => $appointment->id,
            'status' => ClinicalSessionLog::STATUS_DRAFT,
            'treatment_activities' => 'Segundo autoguardado.',
            'patient_response' => 'Respuesta actualizada.',
        ]);

        $log = ClinicalSessionLog::query()->where('appointment_id', $appointment->id)->firstOrFail();
        $this->assertSame(1, AuditEvent::query()
            ->where('event', 'clinical_session_log.created')
            ->where('target_type', $log->getMorphClass())
            ->where('target_id', (string) $log->id)
            ->count());
    }

    public function test_completed_session_rejects_autosave_and_save_and_exit_returns_to_sessions(): void
    {
        [$user, $therapist] = $this->linkedTherapist();
        [$patient, $therapy] = $this->clinicalEntities();
        $appointment = $this->appointment($patient, $therapy, $therapist, '2026-09-09 12:00:00');

        $this->actingAs($user)
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [$therapist->id],
                'treatment_activities' => 'Borrador manual.',
                'complete' => 0,
                'save_and_exit' => 1,
            ])
            ->assertRedirect(route('session-logs.index'));

        $log = ClinicalSessionLog::query()->where('appointment_id', $appointment->id)->firstOrFail();
        $log->update([
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson(route('session-logs.autosave', $appointment), [
                'participant_ids' => [$therapist->id],
                'treatment_activities' => 'No debe sobrescribirse.',
            ])
            ->assertForbidden();

        $this->assertSame('Borrador manual.', $log->fresh()->treatment_activities);
    }

    private function linkedTherapist(): array
    {
        $user = User::factory()->create([
            'name' => 'Terapeuta Workspace',
            'email' => 'workspace@urpe.test',
            'is_active' => true,
        ]);
        $user->assignRole('therapist');

        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => 'Terapeuta Workspace',
            'email' => 'workspace@urpe.test',
            'is_active' => true,
        ]);

        return [$user, $therapist];
    }

    private function clinicalEntities(): array
    {
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Workspace',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);

        $therapy = Therapy::query()->create([
            'name' => 'Terapia Workspace',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#7c3aed',
            'is_active' => true,
        ]);

        return [$patient, $therapy];
    }

    private function appointment(Patient $patient, Therapy $therapy, Therapist $therapist, string $startsAt): Appointment
    {
        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => $startsAt,
            'ends_at' => date('Y-m-d H:i:s', strtotime($startsAt.' +40 minutes')),
            'duration_minutes' => 40,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        $appointment->therapists()->attach($therapist);

        return $appointment;
    }
}
