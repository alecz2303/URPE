<?php

namespace App\Http\Controllers;

use App\Services\AuditTrail;
use App\Services\CenterConfiguration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CenterConfigurationController extends Controller
{
    public function edit(CenterConfiguration $configuration): View
    {
        $this->authorize('center.manage');

        return view('center.edit', [
            'center' => $configuration->settings(),
            'weeklyHours' => $configuration->weeklyHours(),
            'days' => [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
                7 => 'Domingo',
            ],
        ]);
    }

    public function update(
        Request $request,
        CenterConfiguration $configuration,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('center.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string', 'max:500'],
            'timezone' => ['required', 'timezone'],
            'hours' => ['required', 'array'],
            'hours.*' => ['array'],
            'hours.*.*' => ['array'],
            'hours.*.*.is_enabled' => ['nullable', 'boolean'],
            'hours.*.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.*.closes_at' => ['nullable', 'date_format:H:i'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'Ingresa un correo electrónico válido.',
            'timezone' => 'La zona horaria seleccionada no es válida.',
            'date_format' => 'El campo :attribute debe tener formato HH:MM.',
        ], [
            'name' => 'nombre del centro',
            'timezone' => 'zona horaria',
            'hours' => 'horarios',
        ]);

        $hours = collect($validated['hours'])
            ->mapWithKeys(function (array $windows, int|string $day): array {
                $normalized = collect($windows)
                    ->map(fn (array $window): array => [
                        'is_enabled' => (bool) ($window['is_enabled'] ?? false),
                        'opens_at' => $window['opens_at'] ?? null,
                        'closes_at' => $window['closes_at'] ?? null,
                    ])
                    ->values()
                    ->all();

                return [(int) $day => $normalized];
            })
            ->all();

        $configuration->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'timezone' => $validated['timezone'],
        ], $hours, $request->user(), $audit);

        return redirect()
            ->route('center.edit')
            ->with('status', 'Configuración del centro actualizada correctamente.');
    }
}
