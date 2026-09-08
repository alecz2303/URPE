<?php

namespace Tests\Feature;

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

class ClinicalSessionLogHistoricalAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_replaced_historical_participant_can_view_but_cannot_edit_session_log(): void
    {
        [$originalUser, $originalTherapist] = $this->linkedTherapist('Terapeuta Original', 'original-historico@urpe.test');
        [$replacementUser, $replacementTherapist] = $this->linkedTherapist('Terapeuta Sustituto', 'sustituto-historico@urpe.test');
        $this->availability($originalTherapist);
        $this->availability($replacementTherapist);

        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Histórico',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Terapia Histórica',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3366FF',
            'is_active' => true,
        ]);
        $actor = User::factory()->create();

        $appointment = app(AppointmentScheduler::class)->create(
            $patient,
            $therapy,
            [$originalTherapist->id],
            CarbonImmutable::parse('2026-09-14 10:00:00'),
            $actor,
        );

        $this->actingAs($originalUser)
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [$originalTherapist->id],
                'treatment_activities' => 'Captura inicial antes de la sustitución.',
                'complete' => 0,
            ])
            ->assertRedirect(route('session-logs.show', $appointment));

        $log = ClinicalSessionLog::query()->where('appointment_id', $appointment->id)->firstOrFail();
        $this->assertTrue($log->participatingTherapists()->whereKey($originalTherapist->id)->exists());

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->patch(route('appointments.therapists.substitute', $appointment), [
                'removed_therapist_id' => $originalTherapist->id,
                'added_therapist_id' => $replacementTherapist->id,
                'reason' => 'Sustitución posterior a captura inicial.',
            ])
            ->assertSessionHasNoErrors();

        $this->actingAs($originalUser)
            ->get(route('session-logs.show', $appointment))
            ->assertOk();

        $this->actingAs($originalUser)
            ->get(route('session-logs.edit', $appointment))
            ->assertForbidden();

        $this->actingAs($originalUser)
            ->put(route('session-logs.update', $appointment), [
                'participant_ids' => [$originalTherapist->id],
                'treatment_activities' => 'No debe poder modificar después de ser reemplazado.',
                'complete' => 0,
            ])
            ->assertForbidden();

        $this->actingAs($replacementUser)
            ->get(route('session-logs.edit', $appointment))
            ->assertOk();

        $this->assertDatabaseHas('clinical_session_logs', [
            'id' => $log->id,
            'treatment_activities' => 'Captura inicial antes de la sustitución.',
        ]);
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
