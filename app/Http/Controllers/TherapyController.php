<?php

namespace App\Http\Controllers;

use App\Models\Therapy;
use App\Services\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TherapyController extends Controller
{
    public function index(): View
    {
        $this->authorize('therapies.manage');

        return view('therapies.index', [
            'therapies' => Therapy::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('therapies.manage');

        return view('therapies.create');
    }

    public function store(Request $request, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('therapies.manage');

        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        $therapy = Therapy::query()->create([
            'name' => trim($validated['name']),
            'duration_minutes' => (int) $validated['duration_minutes'],
            'required_therapists' => (int) $validated['required_therapists'],
            'color' => strtoupper($validated['color']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        $audit->record('therapy.created', $therapy, [
            'name' => $therapy->name,
            'duration_minutes' => $therapy->duration_minutes,
            'required_therapists' => $therapy->required_therapists,
            'color' => $therapy->color,
            'is_active' => $therapy->is_active,
        ], $request->user(), $request);

        return redirect()->route('therapies.edit', $therapy)
            ->with('status', 'Terapia creada correctamente.');
    }

    public function edit(Therapy $therapy): View
    {
        $this->authorize('therapies.manage');

        return view('therapies.edit', compact('therapy'));
    }

    public function update(Request $request, Therapy $therapy, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('therapies.manage');

        $validated = $request->validate($this->rules($therapy), $this->messages(), $this->attributes());

        $before = $therapy->only([
            'name', 'duration_minutes', 'required_therapists', 'color', 'is_active',
        ]);

        $therapy->update([
            'name' => trim($validated['name']),
            'duration_minutes' => (int) $validated['duration_minutes'],
            'required_therapists' => (int) $validated['required_therapists'],
            'color' => strtoupper($validated['color']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        $audit->record('therapy.updated', $therapy, [
            'before' => $before,
            'after' => $therapy->only([
                'name', 'duration_minutes', 'required_therapists', 'color', 'is_active',
            ]),
        ], $request->user(), $request);

        return redirect()->route('therapies.edit', $therapy)
            ->with('status', 'Terapia actualizada correctamente.');
    }

    public function toggleActive(Request $request, Therapy $therapy, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('therapies.manage');

        $before = $therapy->is_active;
        $therapy->update(['is_active' => ! $before]);

        $audit->record('therapy.status_updated', $therapy, [
            'before' => $before,
            'after' => $therapy->is_active,
        ], $request->user(), $request);

        return redirect()->route('therapies.index')
            ->with('status', $therapy->is_active
                ? 'Terapia activada correctamente.'
                : 'Terapia desactivada correctamente.');
    }

    private function rules(?Therapy $therapy = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('therapies', 'name')->ignore($therapy?->id),
            ],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'required_therapists' => ['required', 'integer', 'min:1', 'max:50'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe ser al menos :min.',
            'unique' => 'Ya existe una terapia con ese nombre.',
            'regex' => 'El color debe tener formato hexadecimal, por ejemplo #3B82F6.',
            'boolean' => 'El campo :attribute no es válido.',
        ];
    }

    private function attributes(): array
    {
        return [
            'name' => 'nombre',
            'duration_minutes' => 'duración',
            'required_therapists' => 'terapeutas requeridos',
            'color' => 'color',
            'is_active' => 'estado activo',
        ];
    }
}
