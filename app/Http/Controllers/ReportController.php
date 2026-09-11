<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicalSessionLog;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->hasPermission('reports.view'), 403);

        $dateFrom = $this->normalizedDate((string) $request->query('date_from', ''), now()->startOfMonth()->toDateString());
        $dateTo = $this->normalizedDate((string) $request->query('date_to', ''), now()->endOfMonth()->toDateString());

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $status = trim((string) $request->query('status', ''));
        if (! array_key_exists($status, Appointment::statuses())) {
            $status = '';
        }

        $therapyId = max(0, (int) $request->query('therapy_id', 0));
        $therapistId = max(0, (int) $request->query('therapist_id', 0));
        $patientId = max(0, (int) $request->query('patient_id', 0));

        $applyAppointmentFilters = function (Builder $query) use ($dateFrom, $dateTo, $status, $therapyId, $therapistId, $patientId): void {
            $query
                ->whereDate('starts_at', '>=', $dateFrom)
                ->whereDate('starts_at', '<=', $dateTo)
                ->when($status !== '', fn (Builder $builder) => $builder->where('status', $status))
                ->when($therapyId > 0, fn (Builder $builder) => $builder->where('therapy_id', $therapyId))
                ->when($patientId > 0, fn (Builder $builder) => $builder->where('patient_id', $patientId))
                ->when($therapistId > 0, fn (Builder $builder) => $builder->whereHas('therapists', fn (Builder $therapists) => $therapists->whereKey($therapistId)));
        };

        $appointmentsQuery = Appointment::query()
            ->with(['patient', 'therapy', 'therapists']);
        $applyAppointmentFilters($appointmentsQuery);

        $statusCounts = (clone $appointmentsQuery)
            ->reorder()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($value) => (int) $value)
            ->all();

        $totalAppointments = array_sum($statusCounts);
        $completedAppointments = $statusCounts[Appointment::STATUS_COMPLETED] ?? 0;
        $noShowAppointments = $statusCounts[Appointment::STATUS_NO_SHOW] ?? 0;
        $cancelledAppointments = $statusCounts[Appointment::STATUS_CANCELLED] ?? 0;
        $attendanceBase = $completedAppointments + $noShowAppointments;
        $attendanceRate = $attendanceBase > 0
            ? round(($completedAppointments / $attendanceBase) * 100, 1)
            : null;

        $appointments = (clone $appointmentsQuery)
            ->orderByDesc('starts_at')
            ->paginate(20, ['*'], 'appointments_page')
            ->withQueryString();

        $sessionsQuery = ClinicalSessionLog::query()
            ->with(['appointment.therapists', 'patient', 'therapy', 'participatingTherapists'])
            ->where('clinical_session_logs.status', ClinicalSessionLog::STATUS_COMPLETED)
            ->whereHas('appointment', function (Builder $appointment) use ($applyAppointmentFilters): void {
                $applyAppointmentFilters($appointment);
            });

        $completedSessionsCount = (clone $sessionsQuery)->count();

        $sessions = $sessionsQuery
            ->join('appointments', 'appointments.id', '=', 'clinical_session_logs.appointment_id')
            ->select('clinical_session_logs.*')
            ->orderByDesc('appointments.starts_at')
            ->orderByDesc('clinical_session_logs.id')
            ->paginate(15, ['clinical_session_logs.*'], 'sessions_page')
            ->withQueryString();

        return view('reports.index', [
            'appointments' => $appointments,
            'sessions' => $sessions,
            'therapies' => Therapy::query()->orderBy('name')->get(['id', 'name']),
            'therapists' => Therapist::query()->orderBy('name')->get(['id', 'name']),
            'patients' => Patient::query()->where('is_active', true)->orderBy('last_name')->orderBy('first_name')->get(),
            'statuses' => Appointment::statuses(),
            'statusCounts' => $statusCounts,
            'totalAppointments' => $totalAppointments,
            'completedAppointments' => $completedAppointments,
            'noShowAppointments' => $noShowAppointments,
            'cancelledAppointments' => $cancelledAppointments,
            'attendanceRate' => $attendanceRate,
            'completedSessionsCount' => $completedSessionsCount,
            'filters' => compact('dateFrom', 'dateTo', 'status', 'therapyId', 'therapistId', 'patientId'),
        ]);
    }

    private function normalizedDate(string $value, string $fallback): string
    {
        $value = trim($value);

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $fallback;
        }

        [$year, $month, $day] = array_map('intval', explode('-', $value));

        return checkdate($month, $day, $year) ? $value : $fallback;
    }
}
