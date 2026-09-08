<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Therapist;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $today = CarbonImmutable::today();

        $data = [
            'today' => $today,
            'todayAppointments' => collect(),
            'todayAppointmentsCount' => null,
            'activePatientsCount' => null,
            'activeTherapistsCount' => null,
            'assignedClinicalSessions' => collect(),
        ];

        if ($user->can('appointments.view')) {
            $data['todayAppointments'] = Appointment::query()
                ->with(['patient', 'therapy', 'therapists'])
                ->whereBetween('starts_at', [$today->startOfDay(), $today->endOfDay()])
                ->orderBy('starts_at')
                ->get();
            $data['todayAppointmentsCount'] = $data['todayAppointments']->count();
        }

        if ($user->hasPermission('session_logs.view') && $user->therapistProfile) {
            $data['assignedClinicalSessions'] = Appointment::query()
                ->with(['patient', 'therapy', 'therapists', 'clinicalSessionLog'])
                ->whereBetween('starts_at', [$today->startOfDay(), $today->endOfDay()])
                ->where('status', '!=', Appointment::STATUS_CANCELLED)
                ->whereHas('therapists', fn ($query) => $query->whereKey($user->therapistProfile->id))
                ->orderBy('starts_at')
                ->get();
        }

        if ($user->can('patients.view')) {
            $data['activePatientsCount'] = Patient::query()->where('is_active', true)->count();
        }

        if ($user->can('therapists.manage')) {
            $data['activeTherapistsCount'] = Therapist::query()->where('is_active', true)->count();
        }

        return view('dashboard', $data);
    }
}
