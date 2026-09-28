<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClosedAppointmentSessionActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_appointment_hides_capture_action_on_dashboard_and_session_detail(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('therapist');
        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => 'Terapeuta cierre',
            'email' => $user->email,
            'is_active' => true,
        ]);
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Cierre',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Terapia cierre',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#7c3aed',
            'is_active' => true,
        ]);
        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => now()->startOfDay()->addHours(10),
            'ends_at' => now()->startOfDay()->addHours(10)->addMinutes(40),
            'duration_minutes' => 40,
            'status' => Appointment::STATUS_COMPLETED,
        ]);
        $appointment->therapists()->attach($therapist);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($patient->full_name)
            ->assertDontSee('Capturar bitácora');

        $this->actingAs($user)
            ->get(route('session-logs.show', $appointment))
            ->assertOk()
            ->assertDontSee('Capturar bitácora');

        $this->actingAs($user)
            ->get(route('session-logs.edit', $appointment))
            ->assertStatus(422);
    }
}
