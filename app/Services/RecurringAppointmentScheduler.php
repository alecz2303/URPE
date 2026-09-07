<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSeries;
use App\Models\Patient;
use App\Models\Therapy;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecurringAppointmentScheduler
{
    public const SCOPE_SINGLE = 'single';
    public const SCOPE_FOLLOWING = 'following';
    public const SCOPE_SERIES = 'series';

    public function __construct(
        private readonly AppointmentScheduler $appointments,
        private readonly AuditTrail $audit,
    ) {
    }

    public function createWeeklySeries(
        Patient $patient,
        Therapy $therapy,
        array $therapistIds,
        CarbonImmutable $firstStartsAt,
        CarbonImmutable $endsOn,
        array $weekdays,
        User $actor,
    ): AppointmentSeries {
        $weekdays = collect($weekdays)
            ->map(fn ($day) => (int) $day)
            ->filter(fn (int $day) => $day >= 1 && $day <= 7)
            ->unique()
            ->sort()
            ->values();

        if ($weekdays->isEmpty()) {
            throw ValidationException::withMessages([
                'recurrence_weekdays' => 'Selecciona al menos un día de la semana para la recurrencia.',
            ]);
        }

        if ($endsOn->startOfDay()->lt($firstStartsAt->startOfDay())) {
            throw ValidationException::withMessages([
                'recurrence_ends_on' => 'La fecha final debe ser igual o posterior a la primera cita.',
            ]);
        }

        $occurrences = $this->buildOccurrences($firstStartsAt, $endsOn, $weekdays);

        if ($occurrences->isEmpty()) {
            throw ValidationException::withMessages([
                'recurrence_weekdays' => 'La configuración no genera ninguna cita dentro del rango indicado.',
            ]);
        }

        $conflicts = [];

        foreach ($occurrences as $startsAt) {
            try {
                $this->appointments->validateCandidate($patient, $therapy, $therapistIds, $startsAt);
            } catch (ValidationException $exception) {
                $message = collect($exception->errors())->flatten()->first() ?? 'Conflicto de agenda.';
                $conflicts[] = $startsAt->format('d/m/Y H:i').' — '.$message;
            }
        }

        if ($conflicts !== []) {
            throw ValidationException::withMessages([
                'recurrence_conflicts' => array_map(
                    fn (string $conflict) => 'No se puede crear la serie: '.$conflict,
                    $conflicts,
                ),
            ]);
        }

        return DB::transaction(function () use ($patient, $therapy, $therapistIds, $firstStartsAt, $endsOn, $weekdays, $actor, $occurrences): AppointmentSeries {
            $series = AppointmentSeries::query()->create([
                'patient_id' => $patient->id,
                'therapy_id' => $therapy->id,
                'starts_at_time' => $firstStartsAt->format('H:i:s'),
                'starts_on' => $firstStartsAt->toDateString(),
                'ends_on' => $endsOn->toDateString(),
                'weekdays' => $weekdays->all(),
            ]);

            foreach ($occurrences as $index => $startsAt) {
                $this->appointments->create(
                    $patient,
                    $therapy,
                    $therapistIds,
                    $startsAt,
                    $actor,
                    $series,
                    $index + 1,
                );
            }

            $this->audit->record('appointment_series.created', $series, [
                'patient_id' => $patient->id,
                'therapy_id' => $therapy->id,
                'weekdays' => $weekdays->all(),
                'starts_on' => $firstStartsAt->toDateString(),
                'ends_on' => $endsOn->toDateString(),
                'occurrences' => $occurrences->count(),
            ], $actor);

            return $series->load(['appointments.therapists', 'patient', 'therapy']);
        });
    }

    public function rescheduleScope(
        Appointment $anchor,
        Therapy $therapy,
        array $therapistIds,
        CarbonImmutable $newStartsAt,
        string $scope,
        User $actor,
    ): Collection {
        $scope = $this->normalizeScope($anchor, $scope);

        if ($scope === self::SCOPE_SINGLE) {
            return collect([$this->appointments->reschedule($anchor, $therapy, $therapistIds, $newStartsAt, $actor)]);
        }

        $anchor->loadMissing(['patient', 'series']);
        $affected = $this->affectedAppointments($anchor, $scope, false);
        $ignoreIds = $affected->pluck('id')->all();
        $deltaSeconds = $newStartsAt->getTimestamp() - $anchor->starts_at->getTimestamp();
        $candidates = $affected->mapWithKeys(fn (Appointment $appointment) => [
            $appointment->id => CarbonImmutable::instance($appointment->starts_at)->addSeconds($deltaSeconds),
        ]);

        $conflicts = [];
        foreach ($affected as $appointment) {
            try {
                $this->appointments->validateCandidate(
                    $appointment->patient,
                    $therapy,
                    $therapistIds,
                    $candidates[$appointment->id],
                    $ignoreIds,
                );
            } catch (ValidationException $exception) {
                $message = collect($exception->errors())->flatten()->first() ?? 'Conflicto de agenda.';
                $conflicts[] = $candidates[$appointment->id]->format('d/m/Y H:i').' — '.$message;
            }
        }

        if ($conflicts !== []) {
            throw ValidationException::withMessages([
                'recurrence_conflicts' => array_map(fn (string $conflict) => 'No se puede reprogramar la serie: '.$conflict, $conflicts),
            ]);
        }

        return DB::transaction(function () use ($anchor, $therapy, $therapistIds, $scope, $actor, $affected, $ignoreIds, $candidates): Collection {
            $updated = collect();
            foreach ($affected as $appointment) {
                $updated->push($this->appointments->reschedule(
                    $appointment,
                    $therapy,
                    $therapistIds,
                    $candidates[$appointment->id],
                    $actor,
                    $ignoreIds,
                ));
            }

            if ($scope === self::SCOPE_SERIES && $anchor->series) {
                $orderedCandidates = $candidates->values()->sort()->values();
                $anchor->series->update([
                    'therapy_id' => $therapy->id,
                    'starts_at_time' => $orderedCandidates->first()->format('H:i:s'),
                    'starts_on' => $orderedCandidates->first()->toDateString(),
                    'ends_on' => $orderedCandidates->last()->toDateString(),
                    'weekdays' => $orderedCandidates->map(fn (CarbonImmutable $date) => $date->isoWeekday())->unique()->sort()->values()->all(),
                ]);
            }

            $this->audit->record('appointment_series.rescheduled', $anchor->series, [
                'anchor_appointment_id' => $anchor->id,
                'scope' => $scope,
                'affected_appointment_ids' => $affected->pluck('id')->values()->all(),
                'therapy_id' => $therapy->id,
                'therapist_ids' => array_values($therapistIds),
            ], $actor);

            return $updated;
        });
    }

    public function cancelScope(Appointment $anchor, ?string $reason, string $scope, User $actor): Collection
    {
        $scope = $this->normalizeScope($anchor, $scope);

        if ($scope === self::SCOPE_SINGLE) {
            return collect([$this->appointments->cancel($anchor, $reason, $actor)]);
        }

        $anchor->loadMissing('series');
        $affected = $this->affectedAppointments($anchor, $scope, true);

        return DB::transaction(function () use ($anchor, $reason, $scope, $actor, $affected): Collection {
            $cancelled = collect();
            foreach ($affected as $appointment) {
                $cancelled->push($this->appointments->cancel($appointment, $reason, $actor));
            }

            $this->audit->record('appointment_series.cancelled', $anchor->series, [
                'anchor_appointment_id' => $anchor->id,
                'scope' => $scope,
                'affected_appointment_ids' => $affected->pluck('id')->values()->all(),
                'reason_provided' => filled($reason),
            ], $actor);

            return $cancelled;
        });
    }

    private function affectedAppointments(Appointment $anchor, string $scope, bool $includeCancelled): Collection
    {
        $query = Appointment::query()
            ->where('appointment_series_id', $anchor->appointment_series_id)
            ->with(['patient', 'therapy', 'therapists', 'series'])
            ->orderBy('series_occurrence');

        if (! $includeCancelled) {
            $query->where('status', '!=', Appointment::STATUS_CANCELLED);
        }

        if ($scope === self::SCOPE_FOLLOWING) {
            $query->where('series_occurrence', '>=', $anchor->series_occurrence);
        }

        return $query->get();
    }

    private function normalizeScope(Appointment $appointment, string $scope): string
    {
        if (! $appointment->isRecurring()) {
            return self::SCOPE_SINGLE;
        }

        if (! in_array($scope, [self::SCOPE_SINGLE, self::SCOPE_FOLLOWING, self::SCOPE_SERIES], true)) {
            throw ValidationException::withMessages([
                'scope' => 'Selecciona un alcance válido para aplicar el cambio.',
            ]);
        }

        return $scope;
    }

    private function buildOccurrences(
        CarbonImmutable $firstStartsAt,
        CarbonImmutable $endsOn,
        Collection $weekdays,
    ): Collection {
        $occurrences = collect();
        $cursor = $firstStartsAt->startOfDay();
        $lastDay = $endsOn->endOfDay();

        while ($cursor->lte($lastDay)) {
            if ($weekdays->contains($cursor->isoWeekday())) {
                $candidate = $cursor->setTime(
                    $firstStartsAt->hour,
                    $firstStartsAt->minute,
                    $firstStartsAt->second,
                );

                if ($candidate->gte($firstStartsAt) && $candidate->lte($lastDay)) {
                    $occurrences->push($candidate);
                }
            }

            $cursor = $cursor->addDay();
        }

        return $occurrences;
    }
}
