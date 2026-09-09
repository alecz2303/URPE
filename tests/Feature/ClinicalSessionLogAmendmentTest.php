<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\CenterOperatingHour;
use App\Models\ClinicalSessionLog;
use App\Models\ClinicalSessionLogAmendment;
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

class ClinicalSessionLogAmendmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_related_therapist_can_append_amendment_without_changing_original_note(): void
    {
        [$appointment, $user, $therapist, $log] = $this->completedSession();
        $original = $log->treatment_activities;

        $this->actingAs($user)
            ->post(route('session-log-amendments.store', $appointment), [
                'reason' => 'Corrección de dato clínico',
                'content' => 'Se precisa que el ejercicio se realizó con apoyo parcial.',
            ])
            ->assertRedirect(route('session-logs.show', $appointment));

        $this->assertDatabaseHas('clinical_session_log_amendments', [
            'clinical_session_log_id' => $log->id,
            'authored_by_user_id' => $user->id,
            'reason' => 'Corrección de dato clínico',
        ]);
        $this->assertSame($original, $log->fresh()->treatment_activities);
        $this->assertSame([$therapist->id], $log->fresh()->participatingTherapists()->pluck('therapists.id')->all());

        $event = AuditEvent::query()->where('event', 'clinical_session_log.amendment_created')->firstOrFail();
        $this->assertFalse((bool) data_get($event->metadata, 'clinical_content_stored_in_audit'));
        $this->assertStringNotContainsString('apoyo parcial', json_encode($event->metadata));
    }

    public function test_amendments_require_completed_log_and_valid_session_relationship(): void
    {
        [$appointment, $user, $therapist, $log] = $this->completedSession();
        [$otherUser] = $this->linkedTherapist('Terapeuta Ajeno', 'ajeno-enmienda@urpe.test');

        $this->actingAs($otherUser)
            ->post(route('session-log-amendments.store', $appointment), [
                'reason' => 'No autorizado',
                'content' => 'No debe persistir.',
            ])
            ->assertForbidden();

        $log->update(['status' => ClinicalSessionLog::STATUS_DRAFT, 'completed_at' => null]);

        $this->actingAs($user)
            ->post(route('session-log-amendments.store', $appointment), [
                'reason' => 'Bitácora abierta',
                'content' => 'No debe persistir.',
            ])
            ->assertStatus(422);

        $this->assertSame(0, ClinicalSessionLogAmendment::query()->count());
        $this->assertTrue($appointment->therapists()->whereKey($therapist->id)->exists());
    }

    public function test_multiple_amendments_are_rendered_chronologically_and_marked_in_patient_timeline(): void
    {
        [$appointment, $user, , $log] = $this->completedSession();

        ClinicalSessionLogAmendment::query()->create([
            'clinical_session_log_id' => $log->id,
            'authored_by_user_id' => $user->id,
            'reason' => 'Primera enmienda',
            'content' => 'Complemento uno.',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        ClinicalSessionLogAmendment::query()->create([
            'clinical_session_log_id' => $log->id,
            'authored_by_user_id' => $user->id,
            'reason' => 'Segunda enmienda',
            'content' => 'Complemento dos.',
        ]);

        $this->actingAs($user)
            ->get(route('session-logs.show', $appointment))
            ->assertOk()
            ->assertSeeInOrder(['Primera enmienda', 'Segunda enmienda']);

        $this->actingAs($user)
            ->get(route('session-logs.patient-history', $appointment->patient))
            ->assertOk()
            ->assertSee('Evolución clínica')
            ->assertSee('2 enmiendas')
            ->assertSee($appointment->therapy->name)
            ->assertSee($appointment->patient->full_name);
    }

    public function test_historical_participant_keeps_read_access_but_cannot_append_amendment_after_substitution(): void
    {
        [$appointment, $originalUser, $originalTherapist, $log] = $this->completedSession();
        [$replacementUser, $replacementTherapist] = $this->linkedTherapist('Terapeuta Sustituto', 'sustituto-enmienda@urpe.test');
        $this->availability($replacementTherapist);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->patch(route('appointments.therapists.substitute', $appointment), [
                'removed_therapist_id' => $originalTherapist->id,
                'added_therapist_id' => $replacementTherapist->id,
                'reason' => 'Sustitución posterior a la sesión.',
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($originalUser)
            ->get(route('session-logs.show', $appointment))
            ->assertOk();

        $this->actingAs($originalUser)
            ->post(route('session-log-amendments.store', $appointment), [
                'reason' => 'Intento histórico',
                'content' => 'No debe permitirse.',
            ])
            ->assertForbidden();

        $this->actingAs($replacementUser)
            ->post(route('session-log-amendments.store', $appointment), [
                'reason' => 'Complemento autorizado',
                'content' => 'Información clínica complementaria.',
            ])
            ->assertRedirect(route('session-logs.show', $appointment));

        $this->assertDatabaseHas('clinical_session_log_amendments', [
            'clinical_session_log_id' => $log->id,
            'authored_by_user_id' => $replacementUser->id,
            'reason' => 'Complemento autorizado',
        ]);
    }

    private function completedSession(): array
    {
        [$user, $therapist] = $this->linkedTherapist('Terapeuta Enmienda', 'enmienda@urpe.test');
        $this->availability($therapist);

        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Evolución',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Terapia Evolución',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#7C3AED',
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

        $log = ClinicalSessionLog::query()->create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'authored_by_user_id' => $user->id,
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'treatment_activities' => 'Nota clínica original que debe permanecer inmutable.',
            'patient_response' => 'Paciente tolera adecuadamente el tratamiento.',
            'completed_at' => now(),
        ]);
        $log->participatingTherapists()->attach($therapist->id);

        return [$appointment, $user, $therapist, $log];
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
