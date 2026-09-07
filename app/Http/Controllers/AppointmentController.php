<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\Therapy;
use App\Services\AppointmentScheduler;
use App\Services\RecurringAppointmentScheduler;
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
            ->with(['patient', 'therapy', 'therapists', 'series'])
            ->whereBetween('starts_at', [$rangeStart, $rangeEnd])
            ->orderBy('starts_at')
            ->get();

        $weekDays = collect(range(0, 6))
            ->map(fn (int $offset) => $date->startOfWeek()->addDays($offset));

        $calendarStart = $date->startOfMonth()->startOfWeek();
        $calendarEnd = $date->endOfMonth()->endOfWeek();
        $calendarDays = collect();

        for ($cursor = $calendarStart; $cursor->lte($calendarEnd); $cursor = $cursor->addDay()) {
            $calendarDays->push($cursor);
        }

        return view('appointments.index', compact(
            'appointments',
            'mode',
            'date',
            'rangeStart',
            'rangeEnd',
            'weekDays',
            'calendarDays',
        ));
    }

    public function create(): View
    {
        Gate::authorize('appointments.manage');

        return view('appointments.create', $this->formData());
    }

    public function store(
        Request $request,
        AppointmentScheduler $scheduler,
        RecurringAppointmentScheduler $recurringScheduler,
    ): RedirectResponse {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'therapy_id' => ['required', 'integer', 'exists:therapies,id'],
            'therapist_ids' => ['required', 'array', 'min:1'],
            'therapist_ids.*' => ['integer', 'distinct', 'exists:therapists,id'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'recurrence_enabled' => ['nullable', 'boolean'],
            'recurrence_weekdays' => ['required_if:recurrence_enabled,1', 'array', 'min:1'],
            'recurrence_weekdays.*' => ['integer', 'between:1,7', 'distinct'],
            'recurrence_ends_on' => ['required_if:recurrence_enabled,1', 'nullable', 'date_format:Y-m-d'],
        ]);

        $patient = Patient::query()->findOrFail($data['patient_id']);
        $therapy = Therapy::query()->findOrFail($data['therapy_id']);
        $startsAt = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['starts_at']);

        if ((bool) ($data['recurrence_enabled'] ?? false)) {
            $series = $recurringScheduler->createWeeklySeries(
                $patient,
                $therapy,
                $data['therapist_ids'],
                $startsAt,
                CarbonImmutable::createFromFormat('Y-m-d', $data['recurrence_ends_on']),
                $data['recurrence_weekdays'],
                $request->user(),
            );

            $firstAppointment = $series->appointments->first();

            return redirect()
                ->route('appointments.index', ['view' => 'day', 'date' => $firstAppointment->starts_at->toDateString()])
                ->with('success', "Serie recurrente creada correctamente con {$series->appointments->count()} citas.");
        }

        $appointment = $scheduler->create(
            $patient,
            $therapy,
            $data['therapist_ids'],
            $startsAt,
            $request->user(),
        );

        return redirect()
            ->route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()])
            ->with('success', 'Cita creada correctamente.');
    }

    public function edit(Appointment $appointment): View
    {
        Gate::authorize('appointments.manage');

        $appointment->load(['patient', 'therapy', 'therapists', 'series']);

        return view('appointments.edit', array_merge(
            $this->formData(),
            ['appointment' => $appointment],
        ));
    }

    public function update(
        Request $request,
        Appointment $appointment,
        AppointmentScheduler $scheduler,
        RecurringAppointmentScheduler $recurringScheduler,
    ): RedirectResponse {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'therapy_id' => ['required', 'integer', 'exists:therapies,id'],
            'therapist_ids' => ['required', 'array', 'min:1'],
            'therapist_ids.*' => ['integer', 'distinct', 'exists:therapists,id'],
            'starts_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'scope' => ['nullable', 'in:single,following,series'],
        ]);

        $therapy = Therapy::query()->findOrFail($data['therapy_id']);
        $startsAt = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['starts_at']);
        $scope = $appointment->isRecurring()
            ? ($data['scope'] ?? RecurringAppointmentScheduler::SCOPE_SINGLE)
            : RecurringAppointmentScheduler::SCOPE_SINGLE;

        if ($appointment->isRecurring()) {
            $updated = $recurringScheduler->rescheduleScope(
                $appointment,
                $therapy,
                $data['therapist_ids'],
                $startsAt,
                $scope,
                $request->user(),
            );
            $message = match ($scope) {
                RecurringAppointmentScheduler::SCOPE_FOLLOWING => "Se reprogramaron {$updated->count()} citas desde esta sesión.",
                RecurringAppointmentScheduler::SCOPE_SERIES => "Se reprogramaron {$updated->count()} citas de la serie.",
                default => 'Cita reprogramada correctamente.',
            };
        } else {
            $scheduler->reschedule(
                $appointment,
                $therapy,
                $data['therapist_ids'],
                $startsAt,
                $request->user(),
            );
            $message = 'Cita reprogramada correctamente.';
        }

        return redirect()
            ->route('appointments.index', ['view' => 'day', 'date' => $startsAt->toDateString()])
            ->with('success', $message);
    }

    public function cancel(
        Request $request,
        Appointment $appointment,
        AppointmentScheduler $scheduler,
        RecurringAppointmentScheduler $recurringScheduler,
    ): RedirectResponse {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
            'scope' => ['nullable', 'in:single,following,series'],
        ]);

        $scope = $appointment->isRecurring()
            ? ($data['scope'] ?? RecurringAppointmentScheduler::SCOPE_SINGLE)
            : RecurringAppointmentScheduler::SCOPE_SINGLE;

        if ($appointment->isRecurring()) {
            $cancelled = $recurringScheduler->cancelScope(
                $appointment,
                $data['cancellation_reason'] ?? null,
                $scope,
                $request->user(),
            );
            $message = match ($scope) {
                RecurringAppointmentScheduler::SCOPE_FOLLOWING => "Se cancelaron {$cancelled->count()} citas desde esta sesión.",
                RecurringAppointmentScheduler::SCOPE_SERIES => "Se cancelaron {$cancelled->count()} citas de la serie.",
                default => 'Cita cancelada correctamente.',
            };
        } else {
            $scheduler->cancel($appointment, $data['cancellation_reason'] ?? null, $request->user());
            $message = 'Cita cancelada correctamente.';
        }

        return back()->with('success', $message);
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
