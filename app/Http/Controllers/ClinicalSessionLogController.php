<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalSessionLog;
use App\Models\Therapist;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Http\JsonResponse;
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

        if (! $appointment->allowsSessionCapture()) {
            abort(422, "No se puede capturar bitácora de una cita {$appointment->statusLabel()}.");
        }

        $appointment->load(['patient', 'therapy', 'therapists', 'clinicalSessionLog.participatingTherapists']);

        if ($appointment->clinicalSessionLog?->isCompleted()) {
            abort(403, 'La bitácora completada está cerrada para edición.');
        }

        $recentSessions = ClinicalSessionLog::query()
            ->with(['therapy', 'participatingTherapists', 'amendments', 'appointment'])
            ->where('clinical_session_logs.patient_id', $appointment->patient_id)
            ->where('clinical_session_logs.appointment_id', '!=', $appointment->id)
            ->where('clinical_session_logs.status', ClinicalSessionLog::STATUS_COMPLETED)
            ->join('appointments', 'appointments.id', '=', 'clinical_session_logs.appointment_id')
            ->select('clinical_session_logs.*')
            ->orderByDesc('appointments.starts_at')
            ->orderByDesc('clinical_session_logs.id')
            ->limit(5)
            ->get();

        return view('clinical-session-logs.edit', [
            'appointment' => $appointment,
            'sessionLog' => $appointment->clinicalSessionLog,
            'participants' => $appointment->therapists,
            'recentSessions' => $recentSessions,
        ]);
    }

    public function autosave(Request $request, Appointment $appointment, AuditTrail $audit): JsonResponse
    {
        $this->authorizeAppointmentAccess($request->user(), $appointment, 'session_logs.manage');

        if (! $appointment->allowsSessionCapture()) {
            return response()->json([
                'message' => "No se puede guardar una sesión para una cita {$appointment->statusLabel()}.",
            ], 422);
        }

        $appointment->load(['therapists', 'clinicalSessionLog.participatingTherapists']);

        if ($appointment->clinicalSessionLog?->isCompleted()) {
            return response()->json(['message' => 'La sesión ya fue completada y está cerrada.'], 403);
        }

        $data = $request->validate([
            'treatment_activities' => ['nullable', 'string', 'max:10000'],
            'patient_response' => ['nullable', 'string', 'max:10000'],
            'observations_incidents' => ['nullable', 'string', 'max:10000'],
            'home_recommendations' => ['nullable', 'string', 'max:10000'],
            'next_session_objectives' => ['nullable', 'string', 'max:10000'],
            'participant_ids' => ['nullable', 'array'],
            'participant_ids.*' => ['integer', 'distinct', 'exists:therapists,id'],
        ]);

        $assignedIds = $appointment->therapists->pluck('id')->map(fn ($id) => (int) $id)->sort()->values();
        $participantIds = collect($data['participant_ids'] ?? $assignedIds->all())
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values();

        if ($participantIds->isEmpty() || $participantIds->diff($assignedIds)->isNotEmpty()) {
            return response()->json([
                'message' => 'Los participantes deben corresponder a terapeutas actualmente asignados a la cita.',
            ], 422);
        }

        $wasCreated = false;

        $sessionLog = DB::transaction(function () use ($request, $appointment, $data, $participantIds, $audit, &$wasCreated): ClinicalSessionLog {
            $log = ClinicalSessionLog::query()->firstOrNew(['appointment_id' => $appointment->id]);
            $wasCreated = ! $log->exists;

            $log->fill([
                'patient_id' => $appointment->patient_id,
                'therapy_id' => $appointment->therapy_id,
                'authored_by_user_id' => $log->authored_by_user_id ?: $request->user()->id,
                'status' => ClinicalSessionLog::STATUS_DRAFT,
                'treatment_activities' => $data['treatment_activities'] ?? null,
                'patient_response' => $data['patient_response'] ?? null,
                'observations_incidents' => $data['observations_incidents'] ?? null,
                'home_recommendations' => $data['home_recommendations'] ?? null,
                'next_session_objectives' => $data['next_session_objectives'] ?? null,
                'completed_at' => null,
            ]);
            $log->save();
            $log->participatingTherapists()->sync($participantIds->all());

            if ($wasCreated) {
                $audit->record('clinical_session_log.created', $log, [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'therapy_id' => $appointment->therapy_id,
                    'participant_ids' => $participantIds->all(),
                    'status' => ClinicalSessionLog::STATUS_DRAFT,
                    'source' => 'autosave',
                    'clinical_content_stored_in_audit' => false,
                ], $request->user(), $request);
            }

            return $log;
        });

        return response()->json([
            'saved' => true,
            'session_log_id' => $sessionLog->id,
            'saved_at' => now()->format('H:i:s'),
        ]);
    }

    public function update(Request $request, Appointment $appointment, AuditTrail $audit): RedirectResponse
    {
        $this->authorizeAppointmentAccess($request->user(), $appointment, 'session_logs.manage');

        if (! $appointment->allowsSessionCapture()) {
            throw ValidationException::withMessages([
                'appointment' => "No se puede capturar bitácora de una cita {$appointment->statusLabel()}.",
            ]);
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
            'save_and_exit' => ['nullable', 'boolean'],
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

        if (! $isCompleting && (bool) ($data['save_and_exit'] ?? false)) {
            return redirect()
                ->route('session-logs.index')
                ->with('success', 'Borrador de sesión guardado correctamente.');
        }

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
