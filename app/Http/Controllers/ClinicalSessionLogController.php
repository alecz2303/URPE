<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalSessionLog;
use App\Models\Therapist;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClinicalSessionLogController extends Controller
{
    public function show(Request $request, Appointment $appointment): View
    {
        $this->authorizeAppointmentAccess($request->user(), $appointment, 'session_logs.view');

        $appointment->load([
            'patient',
            'therapy',
            'therapists',
            'clinicalSessionLog.participatingTherapists',
            'clinicalSessionLog.amendments.author',
            'therapistChanges.removedTherapist',
            'therapistChanges.addedTherapist',
        ]);

        return view('clinical-session-logs.show', [
            'appointment' => $appointment,
            'sessionLog' => $appointment->clinicalSessionLog,
            'canManage' => $this->canAccess($request->user(), $appointment, 'session_logs.manage'),
        ]);
    }

    public function edit(Request $request, Appointment $appointment): View
    {
        $this->authorizeAppointmentAccess($request->user(), $appointment, 'session_logs.manage');

        if ($appointment->isCancelled()) {
            abort(422, 'No se puede capturar bitácora de una cita cancelada.');
        }

        $appointment->load(['patient', 'therapy', 'therapists', 'clinicalSessionLog.participatingTherapists']);

        if ($appointment->clinicalSessionLog?->isCompleted()) {
            abort(403, 'La bitácora completada está cerrada para edición.');
        }

        return view('clinical-session-logs.edit', [
            'appointment' => $appointment,
            'sessionLog' => $appointment->clinicalSessionLog,
            'participants' => $appointment->therapists,
        ]);
    }

    public function update(Request $request, Appointment $appointment, AuditTrail $audit): RedirectResponse
    {
        $this->authorizeAppointmentAccess($request->user(), $appointment, 'session_logs.manage');

        if ($appointment->isCancelled()) {
            throw ValidationException::withMessages(['appointment' => 'No se puede capturar bitácora de una cita cancelada.']);
        }

        $appointment->load(['patient', 'therapy', 'therapists', 'clinicalSessionLog.participatingTherapists']);

        if ($appointment->clinicalSessionLog?->isCompleted()) {
            abort(403, 'La bitácora completada está cerrada para edición.');
        }

        $data = $request->validate([
            'treatment_activities' => ['required', 'string', 'max:10000'],
            'patient_response' => ['nullable', 'string', 'max:10000'],
            'observations_incidents' => ['nullable', 'string', 'max:10000'],
            'home_recommendations' => ['nullable', 'string', 'max:10000'],
            'next_session_objectives' => ['nullable', 'string', 'max:10000'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'distinct', 'exists:therapists,id'],
            'complete' => ['nullable', 'boolean'],
        ]);

        $assignedIds = $appointment->therapists->pluck('id')->map(fn ($id) => (int) $id)->sort()->values();
        $participantIds = collect($data['participant_ids'])->map(fn ($id) => (int) $id)->unique()->sort()->values();

        if ($participantIds->diff($assignedIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'participant_ids' => 'Los participantes deben estar asignados a la cita. Realiza primero una sustitución autorizada en la agenda.',
            ]);
        }

        $isCompleting = (bool) ($data['complete'] ?? false);

        $sessionLog = DB::transaction(function () use ($request, $appointment, $data, $participantIds, $isCompleting, $audit): ClinicalSessionLog {
            $log = ClinicalSessionLog::query()->updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'patient_id' => $appointment->patient_id,
                    'therapy_id' => $appointment->therapy_id,
                    'authored_by_user_id' => $request->user()->id,
                    'status' => $isCompleting ? ClinicalSessionLog::STATUS_COMPLETED : ClinicalSessionLog::STATUS_DRAFT,
                    'treatment_activities' => $data['treatment_activities'],
                    'patient_response' => $data['patient_response'] ?? null,
                    'observations_incidents' => $data['observations_incidents'] ?? null,
                    'home_recommendations' => $data['home_recommendations'] ?? null,
                    'next_session_objectives' => $data['next_session_objectives'] ?? null,
                    'completed_at' => $isCompleting ? now() : null,
                ],
            );

            $log->participatingTherapists()->sync($participantIds->all());

            $audit->record(
                $log->wasRecentlyCreated ? 'clinical_session_log.created' : 'clinical_session_log.updated',
                $log,
                [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'therapy_id' => $appointment->therapy_id,
                    'participant_ids' => $participantIds->all(),
                    'status' => $log->status,
                    'clinical_content_stored_in_audit' => false,
                ],
                $request->user(),
                $request,
            );

            if ($isCompleting) {
                $audit->record('clinical_session_log.completed', $log, [
                    'appointment_id' => $appointment->id,
                    'participant_ids' => $participantIds->all(),
                    'completed_at' => $log->completed_at?->toIso8601String(),
                ], $request->user(), $request);
            }

            return $log;
        });

        return redirect()
            ->route('session-logs.show', $appointment)
            ->with('success', $sessionLog->isCompleted() ? 'Bitácora clínica completada y cerrada correctamente.' : 'Borrador de bitácora guardado correctamente.');
    }

    private function authorizeAppointmentAccess(User $user, Appointment $appointment, string $permission): void
    {
        abort_unless($this->canAccess($user, $appointment, $permission), 403);
    }

    private function canAccess(User $user, Appointment $appointment, string $permission): bool
    {
        if (! $user->hasPermission($permission)) {
            return false;
        }

        if ($user->hasPermission('session_logs.manage_all')) {
            return true;
        }

        $therapist = $user->therapistProfile;
        if (! $therapist instanceof Therapist) {
            return false;
        }

        if ($appointment->therapists()->whereKey($therapist->id)->exists()) {
            return true;
        }

        if ($permission !== 'session_logs.view') {
            return false;
        }

        return $appointment->clinicalSessionLog()
            ->whereHas('participatingTherapists', fn ($query) => $query->whereKey($therapist->id))
            ->exists();
    }
}
