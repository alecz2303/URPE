<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\CenterOperatingHour;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use App\Services\AppointmentScheduler;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AppointmentSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_derives_duration_and_assigns_required_therapists(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();

        $appointment = app(AppointmentScheduler::class)->create(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            $actor,
        );

        $this->assertSame(40, $appointment->duration_minutes);
        $this->assertSame('10:40', $appointment->ends_at->format('H:i'));
        $this->assertSame([$therapist->id], $appointment->therapists->pluck('id')->all());
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment.created']);
    }

    public function test_it_rejects_wrong_therapist_count(): void
    {
        [$actor, $patient, $therapy] = $this->baseline();

        $this->expectException(ValidationException::class);

        app(AppointmentScheduler::class)->create(
            $patient,
            $therapy,
            [],
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            $actor,
        );
    }

    public function test_it_rejects_therapist_overlap_but_allows_slot_after_cancellation(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();
        $scheduler = app(AppointmentScheduler::class);
        $start = CarbonImmutable::parse('2026-09-07 10:00:00');

        $first = $scheduler->create($patient, $therapy, [$therapist->id], $start, $actor);

        try {
            $scheduler->create($patient, $therapy, [$therapist->id], $start->addMinutes(20), $actor);
            $this->fail('Expected overlap validation failure.');
        } catch (ValidationException) {
            $this->assertTrue(true);
        }

        $scheduler->cancel($first, 'Reprogramación solicitada', $actor);

        $replacement = $scheduler->create($patient, $therapy, [$therapist->id], $start, $actor);

        $this->assertSame(Appointment::STATUS_SCHEDULED, $replacement->status);
        $this->assertDatabaseHas('appointments', [
            'id' => $first->id,
            'status' => Appointment::STATUS_CANCELLED,
        ]);
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment.cancelled']);
    }

    public function test_it_rejects_inactive_patient_and_unavailable_therapist(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();
        $scheduler = app(AppointmentScheduler::class);

        $patient->update(['is_active' => false]);

        try {
            $scheduler->create($patient->refresh(), $therapy, [$therapist->id], CarbonImmutable::parse('2026-09-07 10:00:00'), $actor);
            $this->fail('Expected inactive patient validation failure.');
        } catch (ValidationException) {
            $this->assertTrue(true);
        }

        $patient->update(['is_active' => true]);

        $this->expectException(ValidationException::class);
        $scheduler->create($patient->refresh(), $therapy, [$therapist->id], CarbonImmutable::parse('2026-09-07 17:45:00'), $actor);
    }

    private function baseline(): array
    {
        $actor = User::factory()->create();
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Prueba',
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
            'name' => 'Terapeuta Uno',
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

        return [$actor, $patient, $therapy, $therapist];
    }
}
