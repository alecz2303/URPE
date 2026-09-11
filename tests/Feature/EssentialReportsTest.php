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

class EssentialReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_view_operational_report_and_status_counts(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        [$therapy, $therapist] = $this->catalog();
        $completed = $this->appointment('Paciente Completo', $therapy, $therapist, Appointment::STATUS_COMPLETED, '2026-09-10 10:00:00');
        $noShow = $this->appointment('Paciente Ausente', $therapy, $therapist, Appointment::STATUS_NO_SHOW, '2026-09-10 11:00:00');
        $outsidePeriod = $this->appointment('Fuera Del Periodo', $therapy, $therapist, Appointment::STATUS_CANCELLED, '2026-08-01 11:00:00');

        ClinicalSessionLog::query()->create([
            'appointment_id' => $completed->id,
            'patient_id' => $completed->patient_id,
            'therapy_id' => $therapy->id,
            'authored_by_user_id' => $user->id,
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'treatment_activities' => 'NOTA-CLINICA-SECRETA',
            'patient_response' => 'RESPUESTA-CLINICA-SECRETA',
            'completed_at' => '2026-09-10 10:40:00',
        ])->participatingTherapists()->attach($therapist);

        $response = $this->actingAs($user)->get(route('reports.index', [
            'date_from' => '2026-09-10',
            'date_to' => '2026-09-10',
        ]));

        $response
            ->assertOk()
            ->assertSee('Reportes')
            ->assertSee($completed->patient->full_name)
            ->assertSee($noShow->patient->full_name)
            ->assertSee('50.0%')
            ->assertDontSee('NOTA-CLINICA-SECRETA')
            ->assertDontSee('RESPUESTA-CLINICA-SECRETA')
            ->assertViewHas('appointments', function ($appointments) use ($completed, $noShow, $outsidePeriod): bool {
                $ids = $appointments->getCollection()->pluck('id');

                return $ids->contains($completed->id)
                    && $ids->contains($noShow->id)
                    && ! $ids->contains($outsidePeriod->id);
            });
    }

    public function test_filters_apply_to_appointments_and_completed_sessions(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        [$therapy, $therapist] = $this->catalog();
        $otherTherapy = Therapy::query()->create([
            'name' => 'Otra terapia',
            'duration_minutes' => 60,
            'required_therapists' => 1,
            'color' => '#0f766e',
            'is_active' => true,
        ]);
        $otherTherapist = Therapist::query()->create(['name' => 'Otro terapeuta', 'is_active' => true]);

        $visible = $this->appointment('Paciente Visible', $therapy, $therapist, Appointment::STATUS_COMPLETED, '2026-09-09 10:00:00');
        $hidden = $this->appointment('Paciente Oculto', $otherTherapy, $otherTherapist, Appointment::STATUS_COMPLETED, '2026-09-09 12:00:00');
        $logs = [];

        foreach ([$visible, $hidden] as $appointment) {
            $log = ClinicalSessionLog::query()->create([
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'therapy_id' => $appointment->therapy_id,
                'authored_by_user_id' => $user->id,
                'status' => ClinicalSessionLog::STATUS_COMPLETED,
                'treatment_activities' => 'Contenido no visible en reporte.',
                'completed_at' => $appointment->ends_at,
            ]);
            $log->participatingTherapists()->attach($appointment->therapists->first());
            $logs[$appointment->id] = $log;
        }

        $response = $this->actingAs($user)
            ->get(route('reports.index', [
                'date_from' => '2026-09-09',
                'date_to' => '2026-09-09',
                'therapy_id' => $therapy->id,
                'therapist_id' => $therapist->id,
                'patient_id' => $visible->patient_id,
                'status' => Appointment::STATUS_COMPLETED,
            ]));

        $response
            ->assertOk()
            ->assertSee('Paciente Visible')
            ->assertViewHas('appointments', function ($appointments) use ($visible, $hidden): bool {
                return $appointments->getCollection()->pluck('id')->all() === [$visible->id]
                    && ! $appointments->getCollection()->pluck('id')->contains($hidden->id);
            })
            ->assertViewHas('sessions', function ($sessions) use ($logs, $visible, $hidden): bool {
                $ids = $sessions->getCollection()->pluck('id');

                return $ids->contains($logs[$visible->id]->id)
                    && ! $ids->contains($logs[$hidden->id]->id);
            });
    }

    public function test_consultation_direction_receives_report_permission_but_reception_does_not(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $direction = User::factory()->create();
        $direction->assignRole('consultation_direction');
        $reception = User::factory()->create();
        $reception->assignRole('reception');

        $this->actingAs($direction)
            ->get(route('reports.index', ['date_from' => '2026-09-01', 'date_to' => '2026-09-30']))
            ->assertOk();

        $this->actingAs($reception)
            ->get(route('reports.index', ['date_from' => '2026-09-01', 'date_to' => '2026-09-30']))
            ->assertForbidden();
    }

    public function test_user_without_report_permission_cannot_access_report_by_url(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('reports.index'))
            ->assertForbidden();
    }

    public function test_report_permission_backfill_is_idempotent(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $migration = require database_path('migrations/2026_09_10_205000_backfill_report_permissions.php');

        $migration->up();
        $migration->up();

        $this->assertDatabaseHas('permissions', ['slug' => 'reports.view']);

        foreach (['administrator', 'clinical_coordination', 'consultation_direction'] as $role) {
            $this->assertDatabaseHas('permission_role', [
                'role_id' => \App\Models\Role::query()->where('slug', $role)->value('id'),
                'permission_id' => \App\Models\Permission::query()->where('slug', 'reports.view')->value('id'),
            ]);
        }
    }

    private function catalog(): array
    {
        $therapy = Therapy::query()->create([
            'name' => 'Terapia reportable',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#0891b2',
            'is_active' => true,
        ]);
        $therapist = Therapist::query()->create([
            'name' => 'Terapeuta Reportes',
            'is_active' => true,
        ]);

        return [$therapy, $therapist];
    }

    private function appointment(string $patientName, Therapy $therapy, Therapist $therapist, string $status, string $startsAt): Appointment
    {
        [$firstName, $lastName] = explode(' ', $patientName, 2);
        $patient = Patient::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => $startsAt,
            'ends_at' => \Carbon\Carbon::parse($startsAt)->addMinutes($therapy->duration_minutes),
            'duration_minutes' => $therapy->duration_minutes,
            'status' => $status,
        ]);
        $appointment->therapists()->attach($therapist);

        return $appointment->load(['patient', 'therapists']);
    }
}
