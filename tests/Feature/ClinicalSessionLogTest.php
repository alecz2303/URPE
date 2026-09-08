<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CenterOperatingHour;
use App\Models\ClinicalSessionLog;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use App\Services\AppointmentScheduler;
use Carbon\CarbonImmutable;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalSessionLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_assigned_therapist_can_capture_draft_and_complete_session_log(): void
    {
        [$appointment, $therapistUser, $therapist] = $this->appointmentForLinkedTherapist();

        $this->actingAs($therapistUser)
            ->get(route('session-logs.edit', $appointment))
            ->assertOk()
            ->assertSee('Bitácora clínica')
            ->assertSee($appointment->patient->full_name);

        $this->actingAs($therapistUser)
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [$therapist->id],
                'treatment_activities' => 'Trabajo de control postural y carga.',
                'patient_response' => 'Buena tolerancia durante la sesión.',
                'observations_incidents' => 'Sin incidencias.',
                'home_recommendations' => 'Continuar ejercicios indicados.',
                'next_session_objectives' => 'Progresar tiempo de carga.',
                'complete' => 0,
            ])
            ->assertRedirect(route('session-logs.show', $appointment));

        $log = ClinicalSessionLog::query()->where('appointment_id', $appointment->id)->firstOrFail();
        $this->assertSame(ClinicalSessionLog::STATUS_DRAFT, $log->status);
        $this->assertSame([$therapist->id], $log->participatingTherapists()->pluck('therapists.id')->all());

        $this->actingAs($therapistUser)
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [$therapist->id],
                'treatment_activities' => 'Trabajo de control postural y carga.',
                'patient_response' => 'Buena tolerancia durante la sesión.',
                'complete' => 1,
            ])
            ->assertRedirect(route('session-logs.show', $appointment));

        $this->assertDatabaseHas('clinical_session_logs', [
            'appointment_id' => $appointment->id,
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'authored_by_user_id' => $therapistUser->id,
        ]);
        $this->assertDatabaseHas('audit_events', ['event' => 'clinical_session_log.completed']);

        $this->actingAs($therapistUser)
            ->get(route('session-logs.edit', $appointment))
            ->assertForbidden();
    }

    public function test_unrelated_therapist_cannot_access_or_capture_another_appointment_log(): void
    {
        [$appointment] = $this->appointmentForLinkedTherapist();
        [$otherUser] = $this->linkedTherapist('Terapeuta Ajeno', 'ajeno@urpe.test');

        $this->actingAs($otherUser)
            ->get(route('session-logs.show', $appointment))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [],
                'treatment_activities' => 'No debe guardarse.',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('clinical_session_logs', ['appointment_id' => $appointment->id]);
    }

    public function test_authorized_emergency_substitution_preserves_history_and_transfers_session_access(): void
    {
        [$appointment, $originalUser, $originalTherapist] = $this->appointmentForLinkedTherapist();
        [$replacementUser, $replacementTherapist] = $this->linkedTherapist('Terapeuta Sustituto', 'sustituto@urpe.test');
        $this->availability($replacementTherapist);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->patch(route('appointments.therapists.substitute', $appointment), [
                'removed_therapist_id' => $originalTherapist->id,
                'added_therapist_id' => $replacementTherapist->id,
                'reason' => 'Salida de emergencia del terapeuta originalmente programado.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertFalse($appointment->fresh()->therapists()->whereKey($originalTherapist->id)->exists());
        $this->assertTrue($appointment->fresh()->therapists()->whereKey($replacementTherapist->id)->exists());
        $this->assertDatabaseHas('appointment_therapist_changes', [
            'appointment_id' => $appointment->id,
            'removed_therapist_id' => $originalTherapist->id,
            'added_therapist_id' => $replacementTherapist->id,
            'changed_by_user_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment.therapist_substituted']);

        $this->actingAs($originalUser)
            ->get(route('session-logs.edit', $appointment))
            ->assertForbidden();

        $this->actingAs($replacementUser)
            ->get(route('session-logs.edit', $appointment))
            ->assertOk();
    }

    public function test_participants_must_be_currently_assigned_to_the_appointment(): void
    {
        [$appointment, $therapistUser, $therapist] = $this->appointmentForLinkedTherapist();
        [, $otherTherapist] = $this->linkedTherapist('Terapeuta No Asignado', 'noasignado@urpe.test');

        $this->actingAs($therapistUser)
            ->from(route('session-logs.edit', $appointment))
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [$therapist->id, $otherTherapist->id],
                'treatment_activities' => 'Intento inválido.',
                'complete' => 0,
            ])
            ->assertRedirect(route('session-logs.edit', $appointment))
            ->assertSessionHasErrors('participant_ids');

        $this->assertDatabaseMissing('clinical_session_logs', ['appointment_id' => $appointment->id]);
    }

    private function appointmentForLinkedTherapist(): array
    {
        [$therapistUser, $therapist] = $this->linkedTherapist('Terapeuta Asignado', 'asignado@urpe.test');
        $this->availability($therapist);

        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Bitácora',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Vojta Bitácora',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3366FF',
            'is_active' => true,
        ]);
        $actor = User::factory()->create();

        $appointment = app(AppointmentScheduler::class)->create(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-14 10:00:00'),
            $actor,
        );

        return [$appointment, $therapistUser, $therapist];
    }

    private function linkedTherapist(string $name, string $email): array
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'is_active' => true,
        ]);
        $user->assignRole('therapist');

        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => $name,
            'email' => $email,
            'is_active' => true,
        ]);

        return [$user, $therapist];
    }

    private function availability(Therapist $therapist): void
    {
        CenterOperatingHour::query()->firstOrCreate([
            'day_of_week' => 1,
            'opens_at' => '09:00:00',
            'closes_at' => '18:00:00',
        ], [
            'is_enabled' => true,
            'sort_order' => 0,
        ]);

        TherapistAvailabilityWindow::query()->create([
            'therapist_id' => $therapist->id,
            'day_of_week' => 1,
            'is_enabled' => true,
            'starts_at' => '09:00:00',
            'ends_at' => '18:00:00',
            'sort_order' => 0,
        ]);
    }
}
