<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CenterOperatingHour;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_agenda(): void
    {
        $this->get(route('appointments.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('appointments.index'))
            ->assertForbidden();
    }

    public function test_authorized_user_can_view_day_week_and_month_agenda(): void
    {
        $user = $this->administrator();

        foreach (['day', 'week', 'month'] as $view) {
            $this->actingAs($user)
                ->get(route('appointments.index', ['view' => $view, 'date' => '2026-09-07']))
                ->assertOk()
                ->assertSee('Agenda clínica')
                ->assertSee('Nueva cita');
        }
    }

    public function test_dashboard_exposes_agenda_only_with_view_permission(): void
    {
        $authorized = $this->administrator();
        $unauthorized = User::factory()->create();

        $this->actingAs($authorized)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Abrir agenda clínica')
            ->assertSee(route('appointments.index'));

        $this->actingAs($unauthorized)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Abrir agenda clínica')
            ->assertDontSee('data-testid="agenda-dashboard-card"', false);
    }

    public function test_view_only_user_cannot_create_edit_update_or_cancel_appointments(): void
    {
        [$administrator, $patient, $therapy, $therapist] = $this->baseline();
        $appointment = $this->createAppointment($administrator, $patient, $therapy, $therapist, '2026-09-07T10:00');
        $user = $this->viewOnlyUser();

        $this->actingAs($user)->get(route('appointments.create'))->assertForbidden();
        $this->actingAs($user)->get(route('appointments.edit', $appointment))->assertForbidden();
        $this->actingAs($user)->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'therapist_ids' => [$therapist->id],
            'starts_at' => '2026-09-07T12:00',
        ])->assertForbidden();
        $this->actingAs($user)->put(route('appointments.update', $appointment), [
            'therapy_id' => $therapy->id,
            'therapist_ids' => [$therapist->id],
            'starts_at' => '2026-09-07T12:00',
        ])->assertForbidden();
        $this->actingAs($user)->patch(route('appointments.cancel', $appointment), [
            'cancellation_reason' => 'No autorizado',
        ])->assertForbidden();
    }

    public function test_authorized_user_can_create_appointment_from_ui(): void
    {
        [$user, $patient, $therapy, $therapist] = $this->baseline();

        $response = $this->actingAs($user)->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'therapist_ids' => [$therapist->id],
            'starts_at' => '2026-09-07T10:00',
        ]);

        $appointment = Appointment::query()->firstOrFail();

        $response->assertRedirect(route('appointments.index', [
            'view' => 'day',
            'date' => '2026-09-07',
        ]));
        $this->assertSame(40, $appointment->duration_minutes);
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment.created']);
    }

    public function test_authorized_user_can_reschedule_and_change_therapy_duration_from_ui(): void
    {
        [$user, $patient, $therapy, $therapist] = $this->baseline();
        $appointment = $this->createAppointment($user, $patient, $therapy, $therapist, '2026-09-07T10:00');
        $longerTherapy = Therapy::query()->create([
            'name' => 'Terapia intensiva',
            'duration_minutes' => 60,
            'required_therapists' => 1,
            'color' => '#118866',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->put(route('appointments.update', $appointment), [
                'therapy_id' => $longerTherapy->id,
                'therapist_ids' => [$therapist->id],
                'starts_at' => '2026-09-07T12:00',
            ])
            ->assertRedirect(route('appointments.index', [
                'view' => 'day',
                'date' => '2026-09-07',
            ]));

        $appointment->refresh();
        $this->assertSame($longerTherapy->id, $appointment->therapy_id);
        $this->assertSame(60, $appointment->duration_minutes);
        $this->assertSame('12:00', $appointment->starts_at->format('H:i'));
        $this->assertSame('13:00', $appointment->ends_at->format('H:i'));
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment.rescheduled']);
    }

    public function test_reschedule_rejects_overlap_and_preserves_previous_schedule(): void
    {
        [$user, $patient, $therapy, $therapist] = $this->baseline();
        $first = $this->createAppointment($user, $patient, $therapy, $therapist, '2026-09-07T10:00');
        $this->createAppointment($user, $patient, $therapy, $therapist, '2026-09-07T11:00');

        $this->actingAs($user)
            ->from(route('appointments.edit', $first))
            ->put(route('appointments.update', $first), [
                'therapy_id' => $therapy->id,
                'therapist_ids' => [$therapist->id],
                'starts_at' => '2026-09-07T10:40',
            ])
            ->assertRedirect(route('appointments.edit', $first))
            ->assertSessionHasErrors('therapist_ids');

        $first->refresh();
        $this->assertSame('10:00', $first->starts_at->format('H:i'));
        $this->assertSame('10:40', $first->ends_at->format('H:i'));
    }

    public function test_cancelled_appointment_cannot_be_rescheduled(): void
    {
        [$user, $patient, $therapy, $therapist] = $this->baseline();
        $appointment = $this->createAppointment($user, $patient, $therapy, $therapist, '2026-09-07T10:00');

        $this->actingAs($user)->patch(route('appointments.cancel', $appointment), [
            'cancellation_reason' => 'Solicitud familiar',
        ])->assertRedirect();

        $this->actingAs($user)
            ->from(route('appointments.edit', $appointment))
            ->put(route('appointments.update', $appointment), [
                'therapy_id' => $therapy->id,
                'therapist_ids' => [$therapist->id],
                'starts_at' => '2026-09-07T12:00',
            ])
            ->assertRedirect(route('appointments.edit', $appointment))
            ->assertSessionHasErrors('appointment');

        $appointment->refresh();
        $this->assertSame(Appointment::STATUS_CANCELLED, $appointment->status);
        $this->assertSame('10:00', $appointment->starts_at->format('H:i'));
    }

    public function test_authorized_user_can_cancel_appointment_from_ui(): void
    {
        [$user, $patient, $therapy, $therapist] = $this->baseline();
        $appointment = $this->createAppointment($user, $patient, $therapy, $therapist, '2026-09-07T10:00');

        $this->actingAs($user)
            ->patch(route('appointments.cancel', $appointment), [
                'cancellation_reason' => 'Solicitud familiar',
            ])
            ->assertRedirect();

        $this->assertSame(Appointment::STATUS_CANCELLED, $appointment->refresh()->status);
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment.cancelled']);
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
            'slug' => 'agenda_read_only',
        ]);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createAppointment(User $user, Patient $patient, Therapy $therapy, Therapist $therapist, string $startsAt): Appointment
    {
        $this->actingAs($user)->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'therapy_id' => $therapy->id,
            'therapist_ids' => [$therapist->id],
            'starts_at' => $startsAt,
        ])->assertRedirect();

        return Appointment::query()->latest('id')->firstOrFail();
    }

    private function baseline(): array
    {
        $user = $this->administrator();
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Agenda',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Vojta',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3366FF',
            'is_active' => true,
        ]);
        $therapist = Therapist::query()->create([
            'name' => 'Terapeuta Agenda',
            'is_active' => true,
        ]);

        CenterOperatingHour::query()->create([
            'day_of_week' => 1,
            'is_enabled' => true,
            'opens_at' => '09:00:00',
            'closes_at' => '18:00:00',
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

        return [$user, $patient, $therapy, $therapist];
    }
}
