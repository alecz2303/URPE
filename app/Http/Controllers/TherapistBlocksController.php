<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use App\Services\AuditTrail;
use App\Services\TherapistAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TherapistBlocksController extends Controller
{
    public function index(Therapist $therapist): View
    {
        $this->authorize('therapists.manage');

        return view('therapists.blocks', [
            'therapist' => $therapist->load([
                'blocks' => fn ($query) => $query->latest('starts_at'),
            ]),
        ]);
    }

    public function store(
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
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'after' => 'El campo :attribute debe ser posterior al inicio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
        ], [
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

        return redirect()->route('therapists.blocks.index', $therapist)
            ->with('status', 'Bloqueo registrado correctamente.');
    }
}
