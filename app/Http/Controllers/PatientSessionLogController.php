<?php

namespace App\Http\Controllers;

use App\Models\ClinicalSessionLog;
use App\Models\Patient;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientSessionLogController extends Controller
{
    public function __invoke(Request $request, Patient $patient): View
    {
        $user = $request->user();
        abort_unless($user->hasPermission('session_logs.view'), 403);

        $query = ClinicalSessionLog::query()
            ->where('patient_id', $patient->id)
            ->with(['appointment', 'therapy', 'participatingTherapists', 'amendments'])
            ->whereHas('appointment')
            ->orderByDesc(
                \App\Models\Appointment::query()
                    ->select('starts_at')
                    ->whereColumn('appointments.id', 'clinical_session_logs.appointment_id')
                    ->limit(1)
            );

        if (! $user->hasPermission('session_logs.manage_all')) {
            $therapist = $user->therapistProfile;
            abort_unless($therapist instanceof Therapist, 403);

            $hasPatientRelationship = $patient->appointments()
                ->whereHas('therapists', fn ($appointmentQuery) => $appointmentQuery->whereKey($therapist->id))
                ->exists()
                || $patient->clinicalSessionLogs()
                    ->whereHas('participatingTherapists', fn ($logQuery) => $logQuery->whereKey($therapist->id))
                    ->exists();

            abort_unless($hasPatientRelationship, 403);

            $query->where(function ($scope) use ($therapist): void {
                $scope->whereHas('participatingTherapists', fn ($participantQuery) => $participantQuery->whereKey($therapist->id))
                    ->orWhereHas('appointment.therapists', fn ($appointmentQuery) => $appointmentQuery->whereKey($therapist->id));
            });
        }

        return view('clinical-session-logs.patient-history', [
            'patient' => $patient,
            'sessionLogs' => $query->get(),
        ]);
    }
}
