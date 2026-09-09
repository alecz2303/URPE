<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalSessionLog;
use App\Models\ClinicalSessionLogAmendment;
use App\Models\Therapist;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicalSessionLogAmendmentController extends Controller
{
    public function store(Request $request, Appointment $appointment, AuditTrail $audit): RedirectResponse
    {
        $this->authorizeManageAccess($request->user(), $appointment);

        $sessionLog = $appointment->clinicalSessionLog()->firstOrFail();
        abort_unless($sessionLog->isCompleted(), 422, 'Sólo se pueden agregar enmiendas a bitácoras completadas.');

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:10000'],
        ]);

        DB::transaction(function () use ($request, $appointment, $sessionLog, $data, $audit): void {
            $amendment = ClinicalSessionLogAmendment::query()->create([
                'clinical_session_log_id' => $sessionLog->id,
                'authored_by_user_id' => $request->user()->id,
                'reason' => $data['reason'],
                'content' => $data['content'],
            ]);

            $audit->record('clinical_session_log.amendment_created', $amendment, [
                'appointment_id' => $appointment->id,
                'clinical_session_log_id' => $sessionLog->id,
                'patient_id' => $sessionLog->patient_id,
                'therapy_id' => $sessionLog->therapy_id,
                'clinical_content_stored_in_audit' => false,
            ], $request->user(), $request);
        });

        return redirect()
            ->route('session-logs.show', $appointment)
            ->with('success', 'Enmienda clínica agregada correctamente. La bitácora original permanece sin cambios.');
    }

    private function authorizeManageAccess(User $user, Appointment $appointment): void
    {
        abort_unless($user->hasPermission('session_logs.manage'), 403);

        if ($user->hasPermission('session_logs.manage_all')) {
            return;
        }

        $therapist = $user->therapistProfile;
        abort_unless($therapist instanceof Therapist, 403);
        abort_unless($appointment->therapists()->whereKey($therapist->id)->exists(), 403);
    }
}
