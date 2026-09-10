<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentOperationalStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_follow_valid_operational_transitions_and_changes_are_audited(): void
    {
        $user = $this->administrator();
        $appointment = $this->appointment();

        foreach ([
            Appointment::STATUS_CONFIRMED,
            Appointment::STATUS_IN_PROGRESS,
            Appointment::STATUS_COMPLETED,
        ] as $status) {
            $this->actingAs($user)
                ->patch(route('appointments.status', $appointment), ['status' => $status])
                ->assertRedirect();

            $this->assertSame($status, $appointment->refresh()->status);
        }

        $this->assertDatabaseHas('audit_events', [
            'event' => 'appointment.status_changed',
            'target_type' => $appointment->getMorphClass(),
            'target_id' => (string) $appointment->id,
        ]);
        $this->assertSame(
            3,
            \App\Models\AuditEvent::query()->where('event', 'appointment.status_changed')->where('target_id', (string) $appointment->id)->count(),
        );
    }

    public function test_invalid_transition_is_rejected_without_changing_status(): void
    {
        $user = $this->administrator();
        $appointment = $this->appointment();

        $this->actingAs($user)
            ->from(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->patch(route('appointments.status', $appointment), [
                'status' => Appointment::STATUS_COMPLETED,
            ])
            ->assertRedirect(route('appointments.index', ['view' => 'day', 'date' => '2026-09-07']))
            ->assertSessionHasErrors('status');

        $this->assertSame(Appointment::STATUS_SCHEDULED, $appointment->refresh()->status);
    }

    public function test_view_only_user_cannot_change_operational_status(): void
    {
        $appointment = $this->appointment();
        $user = $this->viewOnlyUser();

        $this->actingAs($user)
            ->patch(route('appointments.status', $appointment), [
                'status' => Appointment::STATUS_CONFIRMED,
            ])
            ->assertForbidden();

        $this->assertSame(Appointment::STATUS_SCHEDULED, $appointment->refresh()->status);
    }

    public function test_agenda_filters_by_status_therapy_therapist_and_patient(): void
    {
        $user = $this->administrator();

        $patientA = $this->patient('Ana', 'Filtro');
        $patientB = $this->patient('Bruno', 'Oculto');
        $therapyA = $this->therapy('Vojta filtro');
        $therapyB = $this->therapy('Pediasuit oculto');
        $therapistA = Therapist::query()->create(['name' => 'Terapeuta Filtro', 'is_active' => true]);
        $therapistB = Therapist::query()->create(['name' => 'Terapeuta Oculto', 'is_active' => true]);

        $visible = $this->appointment($patientA, $therapyA, $therapistA, Appointment::STATUS_CONFIRMED, '2026-09-07 10:00:00');
        $hidden = $this->appointment($patientB, $therapyB, $therapistB, Appointment::STATUS_SCHEDULED, '2026-09-07 11:00:00');

        $response = $this->actingAs($user)->get(route('appointments.index', [
            'view' => 'day',
            'date' => '2026-09-07',
            'status' => Appointment::STATUS_CONFIRMED,
            'therapy_id' => $therapyA->id,
            'therapist_id' => $therapistA->id,
            'patient_id' => $patientA->id,
        ]));

        $response->assertOk()
            ->assertViewHas('appointments', fn ($appointments) => $appointments->pluck('id')->all() === [$visible->id])
            ->assertSee($visible->patient->full_name)
            ->assertSee('Confirmada');

        $this->assertNotContains($hidden->id, $response->viewData('appointments')->pluck('id')->all());
    }

    public function test_closed_operational_states_do_not_allow_session_capture(): void
    {
        $user = $this->administrator();
        $appointment = $this->appointment(status: Appointment::STATUS_NO_SHOW);

        $this->actingAs($user)
            ->get(route('session-logs.edit', $appointment))
            ->assertStatus(422);
    }

    public function test_no_show_is_terminal_and_does_not_offer_reopening_transition(): void
    {
        $user = $this->administrator();
        $appointment = $this->appointment(status: Appointment::STATUS_NO_SHOW);

        $this->actingAs($user)
            ->patch(route('appointments.status', $appointment), [
                'status' => Appointment::STATUS_SCHEDULED,
            ])
            ->assertSessionHasErrors('status');

        $this->assertSame([], Appointment::transitionTargets(Appointment::STATUS_NO_SHOW));
        $this->assertSame(Appointment::STATUS_NO_SHOW, $appointment->refresh()->status);
    }

    private function administrator(): User
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('administrator');

        return $user;
    }

    private function viewOnlyUser(): User
    {
        $permission = Permission::query()->firstOrCreate(
            ['slug' => 'appointments.view'],
            ['name' => 'Ver agenda clínica'],
        );
        $role = Role::query()->create([
            'name' => 'Agenda lectura',
            'slug' => 'agenda_read_only_operational',
        ]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function patient(string $firstName = 'Paciente', string $lastName = 'Operativo'): Patient
    {
        return Patient::query()->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
    }

    private function therapy(string $name = 'Terapia operativa'): Therapy
    {
        return Therapy::query()->create([
            'name' => $name,
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3366FF',
            'is_active' => true,
        ]);
    }

    private function appointment(
        ?Patient $patient = null,
        ?Therapy $therapy = null,
        ?Therapist $therapist = null,
        string $status = Appointment::STATUS_SCHEDULED,
        string $startsAt = '2026-09-07 10:00:00',
    ): Appointment {
        $patient ??= $this->patient();
        $therapy ??= $this->therapy();
        $therapist ??= Therapist::query()->create([
            'name' => 'Terapeuta Operativo',
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'starts_at' => $startsAt,
            'ends_at' => \Carbon\CarbonImmutable::parse($startsAt)->addMinutes($therapy->duration_minutes),
            'duration_minutes' => $therapy->duration_minutes,
            'status' => $status,
        ]);
        $appointment->therapists()->attach($therapist->id);

        return $appointment->load(['patient', 'therapy', 'therapists']);
    }
}
