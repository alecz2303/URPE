<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use App\Models\User;
use App\Services\AuditTrail;
use App\Services\CenterConfiguration;
use App\Services\TherapistAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TherapistController extends Controller
{
    public function index(): View
    {
        $this->authorize('therapists.manage');

        return view('therapists.index', [
            'therapists' => Therapist::query()
                ->with(['user', 'availabilityWindows'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(CenterConfiguration $centerConfiguration): View
    {
        $this->authorize('therapists.manage');

        return view('therapists.create', [
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'days' => $this->days(),
            'centerHours' => $centerConfiguration->weeklyHours(),
        ]);
    }

    public function store(
        Request $request,
        TherapistAvailability $availability,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('therapists.manage');

        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());
        $schedule = $availability->validateWeeklySchedule(
            $this->normalizeSchedule($validated['schedule'] ?? []),
        );

        $therapist = Therapist::query()->create([
            'user_id' => $validated['user_id'] ?? null,
            'name' => $validated['name'],
            'professional_title' => $validated['professional_title'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'notes' => $validated['notes'] ?? null,
        ]);

        $availability->replaceWeeklySchedule(
            $therapist,
            $schedule,
            $request->user(),
            $audit,
        );

        $audit->record('therapist.created', $therapist, [
            'user_id' => $therapist->user_id,
            'is_active' => $therapist->is_active,
        ], $request->user(), $request);

        return redirect()->route('therapists.edit', $therapist)
            ->with('status', 'Terapeuta creado correctamente.');
    }

    public function edit(Therapist $therapist, CenterConfiguration $centerConfiguration): View
    {
        $this->authorize('therapists.manage');

        return view('therapists.edit', [
            'therapist' => $therapist->load(['availabilityWindows', 'blocks' => fn ($query) => $query->latest('starts_at')]),
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'days' => $this->days(),
            'centerHours' => $centerConfiguration->weeklyHours(),
        ]);
    }

    public function update(
        Request $request,
        Therapist $therapist,
        TherapistAvailability $availability,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('therapists.manage');

        $validated = $request->validate($this->rules($therapist), $this->messages(), $this->attributes());
        $schedule = $availability->validateWeeklySchedule(
            $this->normalizeSchedule($validated['schedule'] ?? []),
        );

        $before = $therapist->only([
            'user_id', 'name', 'professional_title', 'license_number', 'phone', 'email', 'is_active', 'notes',
        ]);

        $therapist->update([
            'user_id' => $validated['user_id'] ?? null,
            'name' => $validated['name'],
            'professional_title' => $validated['professional_title'] ?? null,
            'license_number' => $validated['license_number'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'notes' => $validated['notes'] ?? null,
        ]);

        $availability->replaceWeeklySchedule(
            $therapist,
            $schedule,
            $request->user(),
            $audit,
        );

        $audit->record('therapist.updated', $therapist, [
            'before' => $before,
            'after' => $therapist->only([
                'user_id', 'name', 'professional_title', 'license_number', 'phone', 'email', 'is_active', 'notes',
            ]),
        ], $request->user(), $request);

        return redirect()->route('therapists.edit', $therapist)
            ->with('status', 'Terapeuta actualizado correctamente.');
    }

    public function storeBlock(
        Request $request,
        Therapist $therapist,
        TherapistAvailability $availability,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('therapists.manage');

        $validated = $request->validate([
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], $this->messages(), [
            'starts_at' => 'inicio del bloqueo',
            'ends_at' => 'fin del bloqueo',
            'reason' => 'motivo',
        ]);

        $availability->addBlock(
            $therapist,
            CarbonImmutable::parse($validated['starts_at']),
            CarbonImmutable::parse($validated['ends_at']),
            $validated['reason'] ?? null,
            $request->user(),
            $audit,
        );

        return redirect()->route('therapists.edit', $therapist)
            ->with('status', 'Bloqueo registrado correctamente.');
    }

    private function rules(?Therapist $therapist = null): array
    {
        return [
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
                Rule::unique('therapists', 'user_id')->ignore($therapist?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'professional_title' => ['nullable', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'schedule' => ['nullable', 'array'],
            'schedule.*' => ['array'],
            'schedule.*.*.starts_at' => ['required_with:schedule.*.*.ends_at', 'nullable', 'date_format:H:i'],
            'schedule.*.*.ends_at' => ['required_with:schedule.*.*.starts_at', 'nullable', 'date_format:H:i'],
        ];
    }

    private function normalizeSchedule(array $schedule): array
    {
        return collect($schedule)
            ->mapWithKeys(function (array $windows, int|string $day): array {
                $normalized = collect($windows)
                    ->filter(fn (array $window): bool => filled($window['starts_at'] ?? null) || filled($window['ends_at'] ?? null))
                    ->map(fn (array $window): array => [
                        'starts_at' => $window['starts_at'] ?? null,
                        'ends_at' => $window['ends_at'] ?? null,
                    ])
                    ->values()
                    ->all();

                return [(int) $day => $normalized];
            })
            ->all();
    }

    private function days(): array
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    private function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'required_with' => 'Completa el campo :attribute para definir correctamente la ventana.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'email' => 'Ingresa un correo electrónico válido.',
            'integer' => 'El campo :attribute no es válido.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'unique' => 'Ese :attribute ya está asociado a otro terapeuta.',
            'boolean' => 'El campo :attribute no es válido.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'after' => 'El campo :attribute debe ser posterior al inicio.',
            'date_format' => 'El campo :attribute debe tener formato HH:MM.',
        ];
    }

    private function attributes(): array
    {
        return [
            'user_id' => 'usuario',
            'name' => 'nombre',
            'professional_title' => 'título profesional',
            'license_number' => 'cédula profesional',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'is_active' => 'estado activo',
            'notes' => 'notas',
            'schedule' => 'horario',
        ];
    }
}
