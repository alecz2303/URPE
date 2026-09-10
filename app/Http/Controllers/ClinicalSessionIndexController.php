<?php

namespace App\Http\Controllers;

use App\Models\ClinicalSessionLog;
use App\Models\Therapist;
use App\Models\Therapy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalSessionIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->hasPermission('session_logs.view'), 403);

        $search = trim((string) $request->query('search', ''));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        $status = trim((string) $request->query('status', ''));
        $therapyId = (int) $request->query('therapy_id', 0);
        $therapistId = (int) $request->query('therapist_id', 0);

        $query = ClinicalSessionLog::query()
            ->with([
                'appointment.therapists',
                'patient',
                'therapy',
                'participatingTherapists',
            ]);

        if (! $user->hasPermission('session_logs.manage_all')) {
            $therapist = $user->therapistProfile;

            if (! $therapist instanceof Therapist) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function (Builder $scope) use ($therapist): void {
                    $scope
                        ->whereHas('appointment.therapists', fn (Builder $therapists) => $therapists->whereKey($therapist->id))
                        ->orWhereHas('participatingTherapists', fn (Builder $participants) => $participants->whereKey($therapist->id));
                });
            }
        }

        $query
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->whereHas('patient', function (Builder $patientQuery) use ($search): void {
                    $patientQuery->where('full_name', 'like', '%'.$search.'%');
                });
            })
            ->when($dateFrom !== '', fn (Builder $builder) => $builder->whereHas('appointment', fn (Builder $appointment) => $appointment->whereDate('starts_at', '>=', $dateFrom)))
            ->when($dateTo !== '', fn (Builder $builder) => $builder->whereHas('appointment', fn (Builder $appointment) => $appointment->whereDate('starts_at', '<=', $dateTo)))
            ->when(in_array($status, [ClinicalSessionLog::STATUS_DRAFT, ClinicalSessionLog::STATUS_COMPLETED], true), fn (Builder $builder) => $builder->where('clinical_session_logs.status', $status))
            ->when($therapyId > 0, fn (Builder $builder) => $builder->where('clinical_session_logs.therapy_id', $therapyId))
            ->when($therapistId > 0, function (Builder $builder) use ($therapistId): void {
                $builder->where(function (Builder $therapistScope) use ($therapistId): void {
                    $therapistScope
                        ->whereHas('participatingTherapists', fn (Builder $participants) => $participants->whereKey($therapistId))
                        ->orWhereHas('appointment.therapists', fn (Builder $assigned) => $assigned->whereKey($therapistId));
                });
            });

        $sessions = $query
            ->join('appointments', 'appointments.id', '=', 'clinical_session_logs.appointment_id')
            ->select('clinical_session_logs.*')
            ->orderByDesc('appointments.starts_at')
            ->orderByDesc('clinical_session_logs.id')
            ->paginate(15)
            ->withQueryString();

        return view('clinical-session-logs.index', [
            'sessions' => $sessions,
            'therapies' => Therapy::query()->orderBy('name')->get(['id', 'name']),
            'therapists' => Therapist::query()->orderBy('name')->get(['id', 'name']),
            'filters' => compact('search', 'dateFrom', 'dateTo', 'status', 'therapyId', 'therapistId'),
        ]);
    }
}
