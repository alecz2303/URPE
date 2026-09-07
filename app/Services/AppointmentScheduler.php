<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentScheduler
{
    public function __construct(
        private readonly TherapistAvailability $availability,
        private readonly AuditTrail $audit,
    ) {
    }

    public function create(
        Patient $patient,
        Therapy $therapy,
        array $therapistIds,
        CarbonInterface $startsAt,
        User $actor,
    ): Appointment {
        $this->assertSelectableEntities($patient, $therapy);
        [$therapists, $endsAt] = $this->validateSchedule($therapy, $therapistIds, $startsAt);

        return DB::transaction(function () use ($patient, $therapy, $therapists, $startsAt, $endsAt, $actor): Appointment {
            $appointment = Appointment::query()->create([
                'patient_id' => $patient->id,
                'therapy_id' => $therapy->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'duration_minutes' => $therapy->duration_minutes,
                'status' => Appointment::STATUS_SCHEDULED,
            ]);

            $appointment->therapists()->sync($therapists->pluck('id')->all());

            $this->audit->record('appointment.created', $appointment, [
                'patient_id' => $patient->id,
                'therapy_id' => $therapy->id,
                'therapist_ids' => $therapists->pluck('id')->values()->all(),
                'starts_at' => $startsAt->toIso8601String(),
                'ends_at' => $endsAt->toIso8601String(),
            ], $actor);

            return $appointment->load(['patient', 'therapy', 'therapists']);
        });
    }

    public function reschedule(
        Appointment $appointment,
        Therapy $therapy,
        array $therapistIds,
        CarbonInterface $startsAt,
        User $actor,
    ): Appointment {
        if ($appointment->isCancelled()) {
            throw ValidationException::withMessages(['appointment' => 'Una cita cancelada no puede reprogramarse.']);
        }

        $appointment->loadMissing('patient');
        $this->assertSelectableEntities($appointment->patient, $therapy);
        [$therapists, $endsAt] = $this->validateSchedule($therapy, $therapistIds, $startsAt, $appointment);

        return DB::transaction(function () use ($appointment, $therapy, $therapists, $startsAt, $endsAt, $actor): Appointment {
            $previous = [
                'therapy_id' => $appointment->therapy_id,
                'starts_at' => $appointment->starts_at?->toIso8601String(),
                'ends_at' => $appointment->ends_at?->toIso8601String(),
                'therapist_ids' => $appointment->therapists()->pluck('therapists.id')->values()->all(),
            ];

            $appointment->update([
                'therapy_id' => $therapy->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'duration_minutes' => $therapy->duration_minutes,
            ]);
            $appointment->therapists()->sync($therapists->pluck('id')->all());

            $this->audit->record('appointment.rescheduled', $appointment, [
                'previous' => $previous,
                'therapy_id' => $therapy->id,
                'starts_at' => $startsAt->toIso8601String(),
                'ends_at' => $endsAt->toIso8601String(),
                'therapist_ids' => $therapists->pluck('id')->values()->all(),
            ], $actor);

            return $appointment->refresh()->load(['patient', 'therapy', 'therapists']);
        });
    }

    public function cancel(Appointment $appointment, ?string $reason, User $actor): Appointment
    {
        if ($appointment->isCancelled()) {
            return $appointment;
        }

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        $this->audit->record('appointment.cancelled', $appointment, [
            'reason_provided' => filled($reason),
            'cancelled_at' => $appointment->cancelled_at?->toIso8601String(),
        ], $actor);

        return $appointment;
    }

    private function assertSelectableEntities(Patient $patient, Therapy $therapy): void
    {
        if (! $patient->is_active) {
            throw ValidationException::withMessages(['patient_id' => 'El paciente seleccionado está inactivo.']);
        }

        if (! $therapy->is_active) {
            throw ValidationException::withMessages(['therapy_id' => 'La terapia seleccionada está inactiva.']);
        }
    }

    private function validateSchedule(
        Therapy $therapy,
        array $therapistIds,
        CarbonInterface $startsAt,
        ?Appointment $ignoreAppointment = null,
    ): array {
        $therapistIds = collect($therapistIds)->map(fn ($id) => (int) $id)->unique()->values();

        if ($therapistIds->count() !== $therapy->required_therapists) {
            throw ValidationException::withMessages([
                'therapist_ids' => "La terapia requiere exactamente {$therapy->required_therapists} terapeuta(s).",
            ]);
        }

        $therapists = Therapist::query()->whereIn('id', $therapistIds)->get();
        if ($therapists->count() !== $therapistIds->count() || $therapists->contains(fn (Therapist $therapist) => ! $therapist->is_active)) {
            throw ValidationException::withMessages(['therapist_ids' => 'Todos los terapeutas seleccionados deben existir y estar activos.']);
        }

        $start = CarbonImmutable::instance($startsAt);
        $endsAt = $start->addMinutes($therapy->duration_minutes);

        if (! $start->isSameDay($endsAt)) {
            throw ValidationException::withMessages(['starts_at' => 'La cita debe iniciar y terminar el mismo día.']);
        }

        foreach ($therapists as $therapist) {
            if (! $this->availability->isAvailableDuring($therapist, $start, $endsAt)) {
                throw ValidationException::withMessages([
                    'therapist_ids' => "{$therapist->name} no está disponible durante toda la cita.",
                ]);
            }

            $overlap = Appointment::query()
                ->where('status', '!=', Appointment::STATUS_CANCELLED)
                ->when($ignoreAppointment, fn ($query) => $query->whereKeyNot($ignoreAppointment->id))
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $start)
                ->whereHas('therapists', fn ($query) => $query->whereKey($therapist->id))
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'therapist_ids' => "{$therapist->name} ya tiene una cita que se traslapa con ese horario.",
                ]);
            }
        }

        return [$therapists, $endsAt];
    }
}
