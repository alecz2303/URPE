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

class ClinicalSessionIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_browse_and_filter_clinical_sessions(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        [$draft, $completed] = $this->seedSessions($user);

        $this->actingAs($user)
            ->get(route('session-logs.index'))
            ->assertOk()
            ->assertSee('Sesiones clínicas')
            ->assertSee($draft->patient->full_name)
            ->assertSee($completed->patient->full_name)
            ->assertSee('Continuar captura')
            ->assertSee('Ver bitácora');

        $this->actingAs($user)
            ->get(route('session-logs.index', ['status' => ClinicalSessionLog::STATUS_COMPLETED]))
            ->assertOk()
            ->assertDontSee($draft->patient->full_name)
            ->assertSee($completed->patient->full_name);
    }

    public function test_therapist_only_sees_assigned_or_historical_sessions(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('therapist');
        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => 'Terapeuta visible',
            'email' => $user->email,
            'is_active' => true,
        ]);

        $otherUser = User::factory()->create();
        [$visible] = $this->seedSessions($otherUser, $therapist);
        [, $hidden] = $this->seedSessions($otherUser);

        $this->actingAs($user)
            ->get(route('session-logs.index'))
            ->assertOk()
            ->assertSee($visible->patient->full_name)
            ->assertDontSee($hidden->patient->full_name);
    }

    public function test_user_without_session_log_permission_cannot_open_index(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('session-logs.index'))
            ->assertForbidden();
    }

    private function seedSessions(User $author, ?Therapist $visibleTherapist = null): array
    {
        $therapy = Therapy::query()->firstOrCreate(
            ['name' => 'Terapia índice '.uniqid()],
            [
                'duration_minutes' => 40,
                'required_therapists' => 1,
                'color' => '#7c3aed',
                'is_active' => true,
            ],
        );

        $draftPatient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Borrador '.uniqid(),
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $completedPatient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Completado '.uniqid(),
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);

        $assignedTherapist = $visibleTherapist ?: Therapist::query()->create([
            'name' => 'Terapeuta '.uniqid(),
            'is_active' => true,
        ]);

        $draftAppointment = Appointment::query()->create([
            'patient_id' => $draftPatient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => '2026-09-09 10:00:00',
            'ends_at' => '2026-09-09 10:40:00',
            'duration_minutes' => 40,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        $draftAppointment->therapists()->attach($assignedTherapist);

        $completedAppointment = Appointment::query()->create([
            'patient_id' => $completedPatient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => '2026-09-08 10:00:00',
            'ends_at' => '2026-09-08 10:40:00',
            'duration_minutes' => 40,
            'status' => Appointment::STATUS_SCHEDULED,
        ]);
        $completedAppointment->therapists()->attach(
            $visibleTherapist ?: Therapist::query()->create([
                'name' => 'Terapeuta completado '.uniqid(),
                'is_active' => true,
            ])
        );

        $draft = ClinicalSessionLog::query()->create([
            'appointment_id' => $draftAppointment->id,
            'patient_id' => $draftPatient->id,
            'therapy_id' => $therapy->id,
            'authored_by_user_id' => $author->id,
            'status' => ClinicalSessionLog::STATUS_DRAFT,
            'treatment_activities' => 'Trabajo de borrador.',
        ]);
        $draft->participatingTherapists()->attach($assignedTherapist);

        $completed = ClinicalSessionLog::query()->create([
            'appointment_id' => $completedAppointment->id,
            'patient_id' => $completedPatient->id,
            'therapy_id' => $therapy->id,
            'authored_by_user_id' => $author->id,
            'status' => ClinicalSessionLog::STATUS_COMPLETED,
            'treatment_activities' => 'Trabajo completado.',
            'completed_at' => now(),
        ]);

        return [$draft->load('patient'), $completed->load('patient')];
    }
}
