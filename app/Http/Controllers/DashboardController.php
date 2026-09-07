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
        ];

        if ($user->can('appointments.view')) {
            $data['todayAppointments'] = Appointment::query()
                ->with(['patient', 'therapy', 'therapists'])
                ->whereBetween('starts_at', [$today->startOfDay(), $today->endOfDay()])
                ->orderBy('starts_at')
                ->get();
            $data['todayAppointmentsCount'] = $data['todayAppointments']->count();
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
