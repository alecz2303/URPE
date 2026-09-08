<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Therapist;
use App\Services\AppointmentScheduler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AppointmentTherapistSubstitutionController extends Controller
{
    public function __invoke(Request $request, Appointment $appointment, AppointmentScheduler $scheduler): RedirectResponse
    {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'removed_therapist_id' => ['required', 'integer', 'exists:therapists,id'],
            'added_therapist_id' => ['required', 'integer', 'different:removed_therapist_id', 'exists:therapists,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $removedTherapist = Therapist::query()->findOrFail($data['removed_therapist_id']);
        $addedTherapist = Therapist::query()->findOrFail($data['added_therapist_id']);

        $scheduler->substituteTherapist(
            $appointment,
            $removedTherapist,
            $addedTherapist,
            $request->user(),
            $data['reason'] ?? null,
        );

        return back()->with('success', "Sustitución registrada: {$removedTherapist->name} → {$addedTherapist->name}.");
    }
}
