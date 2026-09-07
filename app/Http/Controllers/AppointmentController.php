<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Services\AppointmentScheduler;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('appointments.view');

        $mode = in_array($request->string('view')->toString(), ['day', 'week', 'month'], true)
            ? $request->string('view')->toString()
            : 'week';

        $date = $this->parseDate($request->string('date')->toString());

        [$rangeStart, $rangeEnd] = match ($mode) {
            'day' => [$date->startOfDay(), $date->endOfDay()],
            'month' => [$date->startOfMonth()->startOfDay(), $date->endOfMonth()->endOfDay()],
            default => [$date->startOfWeek()->startOfDay(), $date->endOfWeek()->endOfDay()],
        };

        $appointments = Appointment::query()
            ->with(['patient', 'therapy', 'therapists'])
            ->whereBetween('starts_at', [$rangeStart, $rangeEnd])
            ->orderBy('starts_at')
            ->get();

        return view('appointments.index', compact(
            'appointments',
            'mode',
            'date',
            'rangeStart',
            'rangeEnd',
        ));
    }

    public function create(): View
    {
        Gate::authorize('appointments.manage');

        return view('appointments.create', $this->formData());
    }

    public function store(Request $request, AppointmentScheduler $scheduler): RedirectResponse
    {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'therapy_id' => ['required', 'integer', 'exists:therapies,id'],
            'therapist_ids' => ['required', 'array', 'min:1'],
            'therapist_ids.*' => ['integer', 'distinct', 'exists:therapists,id'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);

        $appointment = $scheduler->create(
            Patient::query()->findOrFail($data['patient_id']),
            Therapy::query()->findOrFail($data['therapy_id']),
            $data['therapist_ids'],
            CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['starts_at']),
            $request->user(),
        );

        return redirect()
            ->route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()])
            ->with('success', 'Cita creada correctamente.');
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('appointments.manage');

        $appointment->load(['patient', 'therapy', 'therapists']);

        return view('appointments.edit', array_merge(
            $this->formData(),
            ['appointment' => $appointment],
        ));
    }

    public function update(Request $request, Appointment $appointment, AppointmentScheduler $scheduler): RedirectResponse
    {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'therapy_id' => ['required', 'integer', 'exists:therapies,id'],
            'therapist_ids' => ['required', 'array', 'min:1'],
            'therapist_ids.*' => ['integer', 'distinct', 'exists:therapists,id'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);

        $scheduler->reschedule(
            $appointment,
            Therapy::query()->findOrFail($data['therapy_id']),
            $data['therapist_ids'],
            CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['starts_at']),
            $request->user(),
        );

        return redirect()
            ->route('appointments.index', ['view' => 'day', 'date' => $data['starts_at'] ? substr($data['starts_at'], 0, 10) : now()->toDateString()])
            ->with('success', 'Cita reprogramada correctamente.');
    }

    public function cancel(Request $request, Appointment $appointment, AppointmentScheduler $scheduler): RedirectResponse
    {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $scheduler->cancel($appointment, $data['cancellation_reason'] ?? null, $request->user());

        return back()->with('success', 'Cita cancelada correctamente.');
    }

    private function formData(): array
    {
        return [
            'patients' => Patient::query()->where('is_active', true)->orderBy('last_name')->orderBy('first_name')->get(),
            'therapies' => Therapy::query()->where('is_active', true)->orderBy('name')->get(),
            'therapists' => Therapist::query()->where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function parseDate(string $value): CarbonImmutable
    {
        if ($value === '') {
            return CarbonImmutable::today();
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return CarbonImmutable::today();
        }
    }
}
