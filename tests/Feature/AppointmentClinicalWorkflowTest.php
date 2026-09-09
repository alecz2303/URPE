<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\ClinicalSessionLog;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentClinicalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_agenda_exposes_capture_continue_and_view_session_log_states(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        [$appointment, $therapist] = $this->appointment($user);

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertSee('Capturar bitácora')
            ->assertSee(route('session-logs.edit', $appointment));

        $log = ClinicalSessionLog::query()->create([
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'therapy_id' => $appointment->therapy_id,
            'authored_by_user_id' => $user->id,
            'status' => ClinicalSessionLog::STATUS_DRAFT,
            'treatment_activities' => 'Trabajo terapéutico inicial.',
        ]);
        $log->participatingTherapists()->attach($therapist);

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertSee('Bitácora pendiente')
            ->assertSee('Continuar captura')
            ->assertSee(route('session-logs.edit', $appointment));

        $log->update([
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertSee('Bitácora completada')
            ->assertSee('Ver bitácora')
            ->assertSee(route('session-logs.show', $appointment));
    }

    public function test_cancelled_appointment_does_not_expose_session_log_action(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        [$appointment] = $this->appointment($user);
        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->assertOk()
            ->assertDontSee('data-testid="appointment-session-log-action"', false);
    }

    public function test_selected_availability_slot_has_explicit_contrast_rule(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('#availability_slots button.text-white', $css);
        $this->assertStringContainsString('!bg-sky-600', $css);
        $this->assertStringContainsString('!text-white', $css);
    }

    private function appointment(User $user): array
    {
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Flujo clínico',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Terapia de lenguaje',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#7c3aed',
            'is_active' => true,
        ]);
        $therapist = Therapist::query()->create([
            'name' => 'Terapeuta Flujo',
            'is_active' => true,
        ]);
        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => '2026-09-07 11:00:00',
            'ends_at' => '2026-09-07 11:40:00',
            'duration_minutes' => 40,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        $appointment->therapists()->attach($therapist);

        return [$appointment, $therapist];
    }
}
