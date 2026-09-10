<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AppointmentStatusManager
{
    public function __construct(private readonly AuditTrail $audit)
    {
    }

    public function transition(Appointment $appointment, string $targetStatus, User $actor): Appointment
    {
        if (! array_key_exists($targetStatus, Appointment::statuses())) {
            throw ValidationException::withMessages([
                'status' => 'El estado solicitado no es válido.',
            ]);
        }

        if (! in_array($targetStatus, Appointment::transitionTargets($appointment->status), true)) {
            throw ValidationException::withMessages([
                'status' => "No se puede cambiar una cita de {$appointment->statusLabel()} a ".Appointment::statuses()[$targetStatus].'.',
            ]);
        }

        $previousStatus = $appointment->status;
        $appointment->update(['status' => $targetStatus]);

        $this->audit->record('appointment.status_changed', $appointment, [
            'previous_status' => $previousStatus,
            'status' => $targetStatus,
        ], $actor);

        return $appointment->refresh();
    }
}
