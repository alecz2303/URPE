<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CenterOperatingHour;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_center_slots_and_only_marks_capacity_as_selectable(): void
    {
        $user = $this->administrator();
        $therapy = Therapy::query()->create([
            'name' => 'Bipedestador',
            'duration_minutes' => 40,
            'required_therapists' => 2,
            'color' => '#3366FF',
            'is_active' => true,
        ]);
        $first = $this->therapist('Alejandro Rueda');
        $second = $this->therapist('Operador de Prueba');

        CenterOperatingHour::query()->create([
            'day_of_week' => 1,
            'is_enabled' => true,
            'opens_at' => '09:00:00',
            'closes_at' => '11:00:00',
            'sort_order' => 0,
        ]);

        foreach ([$first, $second] as $therapist) {
            TherapistAvailabilityWindow::query()->create([
                'therapist_id' => $therapist->id,
                'day_of_week' => 1,
                'is_enabled' => true,
                'starts_at' => '09:00:00',
                'ends_at' => '11:00:00',
                'sort_order' => 0,
            ]);
        }

        $occupied = Appointment::query()->create([
            'patient_id' => $this->patientId(),
            'therapy_id' => $therapy->id,
            'starts_at' => '2026-09-07 09:40:00',
            'ends_at' => '2026-09-07 10:20:00',
            'duration_minutes' => 40,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        $occupied->therapists()->attach($second->id);

        $response = $this->actingAs($user)->getJson(route('appointments.availability', [
            'therapy_id' => $therapy->id,
            'date' => '2026-09-07',
        ]));

        $response->assertOk()
            ->assertJsonPath('center_windows.0.opens_at', '09:00')
            ->assertJsonPath('center_windows.0.closes_at', '11:00')
            ->assertJsonPath('slots.0.label', '09:00 – 09:40')
            ->assertJsonPath('slots.0.available_count', 2)
            ->assertJsonPath('slots.0.selectable', true)
            ->assertJsonPath('slots.1.label', '09:40 – 10:20')
            ->assertJsonPath('slots.1.available_count', 1)
            ->assertJsonPath('slots.1.selectable', false)
            ->assertJsonPath('slots.1.therapists.1.reason', 'Con otra cita')
            ->assertJsonPath('slots.2.label', '10:20 – 11:00')
            ->assertJsonPath('slots.2.selectable', true);
    }

    public function test_view_only_or_unauthorized_user_cannot_query_manage_availability(): void
    {
        $therapy = Therapy::query()->create([
            'name' => 'Terapia',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3366FF',
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create())
            ->getJson(route('appointments.availability', ['therapy_id' => $therapy->id, 'date' => '2026-09-07']))
            ->assertForbidden();
    }

    private function administrator(): User
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        return $user;
    }

    private function therapist(string $name): Therapist
    {
        return Therapist::query()->create([
            'name' => $name,
            'is_active' => true,
        ]);
    }

    private function patientId(): int
    {
        return \App\Models\Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Disponibilidad',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ])->id;
    }
}
