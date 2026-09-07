<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentSeries;
use App\Models\CenterOperatingHour;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use App\Services\AppointmentScheduler;
use App\Services\RecurringAppointmentScheduler;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RecurringAppointmentSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_bounded_weekly_series_as_individual_appointments(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();

        $series = app(RecurringAppointmentScheduler::class)->createWeeklySeries(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-21'),
            [1],
            $actor,
        );

        $this->assertSame(3, $series->appointments->count());
        $this->assertSame(
            ['2026-09-07', '2026-09-14', '2026-09-21'],
            $series->appointments->pluck('starts_at')->map->toDateString()->all(),
        );
        $this->assertSame([1, 2, 3], $series->appointments->pluck('series_occurrence')->all());
        $this->assertTrue($series->appointments->every(fn (Appointment $appointment) => $appointment->isRecurring()));
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment_series.created']);
    }

    public function test_conflict_aborts_entire_series_without_partial_occurrences(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();

        app(AppointmentScheduler::class)->create(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-14 10:00:00'),
            $actor,
        );

        try {
            app(RecurringAppointmentScheduler::class)->createWeeklySeries(
                $patient,
                $therapy,
                [$therapist->id],
                CarbonImmutable::parse('2026-09-07 10:00:00'),
                CarbonImmutable::parse('2026-09-21'),
                [1],
                $actor,
            );

            $this->fail('Expected recurring conflict validation failure.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('recurrence_conflicts', $exception->errors());
            $this->assertStringContainsString('14/09/2026 10:00', $exception->errors()['recurrence_conflicts'][0]);
        }

        $this->assertSame(1, Appointment::query()->count());
        $this->assertSame(0, AppointmentSeries::query()->count());
    }

    public function test_following_scope_reschedules_anchor_and_future_occurrences_only(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();
        $scheduler = app(RecurringAppointmentScheduler::class);
        $series = $scheduler->createWeeklySeries(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-21'),
            [1],
            $actor,
        );
        $anchor = $series->appointments->get(1);

        $updated = $scheduler->rescheduleScope(
            $anchor,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-14 11:00:00'),
            RecurringAppointmentScheduler::SCOPE_FOLLOWING,
            $actor,
        );

        $series->refresh()->load('appointments');
        $this->assertSame(2, $updated->count());
        $this->assertSame(['10:00', '11:00', '11:00'], $series->appointments->pluck('starts_at')->map->format('H:i')->all());
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment_series.rescheduled']);
    }

    public function test_series_reschedule_is_atomic_when_one_future_occurrence_conflicts(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();
        $scheduler = app(RecurringAppointmentScheduler::class);
        $series = $scheduler->createWeeklySeries(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-21'),
            [1],
            $actor,
        );
        app(AppointmentScheduler::class)->create(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-21 11:00:00'),
            $actor,
        );

        try {
            $scheduler->rescheduleScope(
                $series->appointments->first(),
                $therapy,
                [$therapist->id],
                CarbonImmutable::parse('2026-09-07 11:00:00'),
                RecurringAppointmentScheduler::SCOPE_SERIES,
                $actor,
            );
            $this->fail('Expected recurring reschedule conflict validation failure.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('recurrence_conflicts', $exception->errors());
        }

        $series->refresh()->load('appointments');
        $this->assertSame(['10:00', '10:00', '10:00'], $series->appointments->pluck('starts_at')->map->format('H:i')->all());
    }

    public function test_series_scope_cancels_every_occurrence(): void
    {
        [$actor, $patient, $therapy, $therapist] = $this->baseline();
        $scheduler = app(RecurringAppointmentScheduler::class);
        $series = $scheduler->createWeeklySeries(
            $patient,
            $therapy,
            [$therapist->id],
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-21'),
            [1],
            $actor,
        );

        $cancelled = $scheduler->cancelScope(
            $series->appointments->get(1),
            'Cambio de tratamiento',
            RecurringAppointmentScheduler::SCOPE_SERIES,
            $actor,
        );

        $this->assertSame(3, $cancelled->count());
        $this->assertSame(3, Appointment::query()->where('status', Appointment::STATUS_CANCELLED)->count());
        $this->assertDatabaseHas('audit_events', ['event' => 'appointment_series.cancelled']);
    }

    private function baseline(): array
    {
        $actor = User::factory()->create();
        $patient = Patient::query()->create([
            'first_name' => 'Paciente',
            'last_name' => 'Recurrente',
            'date_of_birth' => '2020-01-01',
            'is_active' => true,
        ]);
        $therapy = Therapy::query()->create([
            'name' => 'Vojta recurrente',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'color' => '#3366FF',
            'is_active' => true,
        ]);
        $therapist = Therapist::query()->create([
            'name' => 'Terapeuta Recurrente',
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
