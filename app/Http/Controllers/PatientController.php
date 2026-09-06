<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Patient;
use App\Services\AuditTrail;
use App\Services\PatientGuardianManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(): View
    {
        $this->authorize('patients.view');

        return view('patients.index', [
            'patients' => Patient::query()
                ->with(['guardians' => fn ($query) => $query->orderByPivot('is_primary', 'desc')])
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('patients.manage');

        return view('patients.create');
    }

    public function store(Request $request, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('patients.manage');

        $validated = $request->validate($this->patientRules(), $this->patientMessages(), $this->patientAttributes());

        $patient = Patient::query()->create($this->patientPayload($validated));

        $audit->record('patient.created', $patient, [
            'folio' => $patient->folio,
            'name' => $patient->full_name,
            'date_of_birth' => optional($patient->date_of_birth)->format('Y-m-d'),
            'is_active' => $patient->is_active,
        ], $request->user(), $request);

        return redirect()->route('patients.show', $patient)
            ->with('status', 'Paciente creado correctamente.');
    }

    public function show(Patient $patient): View
    {
        $this->authorize('patients.view');

        $patient->load(['guardians' => fn ($query) => $query->orderByPivot('is_primary', 'desc')->orderBy('last_name')]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        $this->authorize('patients.manage');

        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('patients.manage');

        $validated = $request->validate($this->patientRules(), $this->patientMessages(), $this->patientAttributes());
        $before = $this->patientAuditSnapshot($patient);

        $patient->update($this->patientPayload($validated));

        $audit->record('patient.updated', $patient, [
            'before' => $before,
            'after' => $this->patientAuditSnapshot($patient->refresh()),
        ], $request->user(), $request);

        return redirect()->route('patients.show', $patient)
            ->with('status', 'Paciente actualizado correctamente.');
    }

    public function toggleActive(Request $request, Patient $patient, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('patients.manage');

        $before = $patient->is_active;
        $patient->update(['is_active' => ! $before]);

        $audit->record('patient.status_updated', $patient, [
            'before' => $before,
            'after' => $patient->is_active,
        ], $request->user(), $request);

        return redirect()->route('patients.show', $patient)
            ->with('status', $patient->is_active
                ? 'Paciente activado correctamente.'
                : 'Paciente desactivado correctamente.');
    }

    public function storeGuardian(
        Request $request,
        Patient $patient,
        PatientGuardianManager $manager,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('patients.manage');

        $validated = $request->validate($this->guardianRules(), $this->guardianMessages(), $this->guardianAttributes());

        $guardian = DB::transaction(function () use ($validated, $patient, $manager): Guardian {
            $guardian = Guardian::query()->create($this->guardianPayload($validated));
            $manager->link(
                $patient,
                $guardian,
                $validated['relationship'] ?? null,
                (bool) ($validated['is_primary'] ?? false),
            );

            return $guardian;
        });

        $audit->record('guardian.created', $guardian, [
            'patient_id' => $patient->id,
            'patient_folio' => $patient->folio,
            'name' => $guardian->full_name,
            'relationship' => $validated['relationship'] ?? null,
            'is_primary' => (bool) ($validated['is_primary'] ?? false),
        ], $request->user(), $request);

        return redirect()->route('patients.show', $patient)
            ->with('status', 'Responsable agregado correctamente.');
    }

    public function updateGuardian(
        Request $request,
        Patient $patient,
        Guardian $guardian,
        PatientGuardianManager $manager,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('patients.manage');
        $this->ensureGuardianLinked($patient, $guardian);

        $validated = $request->validate($this->guardianRules(), $this->guardianMessages(), $this->guardianAttributes());
        $before = $this->guardianAuditSnapshot($patient, $guardian);

        DB::transaction(function () use ($validated, $patient, $guardian, $manager): void {
            $guardian->update($this->guardianPayload($validated));
            $manager->link(
                $patient,
                $guardian,
                $validated['relationship'] ?? null,
                (bool) ($validated['is_primary'] ?? false),
            );
        });

        $patient->load('guardians');
        $guardian->refresh();

        $audit->record('guardian.updated', $guardian, [
            'patient_id' => $patient->id,
            'patient_folio' => $patient->folio,
            'before' => $before,
            'after' => $this->guardianAuditSnapshot($patient, $guardian),
        ], $request->user(), $request);

        return redirect()->route('patients.show', $patient)
            ->with('status', 'Responsable actualizado correctamente.');
    }

    public function setPrimaryGuardian(
        Request $request,
        Patient $patient,
        Guardian $guardian,
        PatientGuardianManager $manager,
        AuditTrail $audit,
    ): RedirectResponse {
        $this->authorize('patients.manage');
        $this->ensureGuardianLinked($patient, $guardian);

        $manager->setPrimary($patient, $guardian);

        $audit->record('guardian.primary_updated', $guardian, [
            'patient_id' => $patient->id,
            'patient_folio' => $patient->folio,
            'guardian_id' => $guardian->id,
        ], $request->user(), $request);

        return redirect()->route('patients.show', $patient)
            ->with('status', 'Responsable principal actualizado correctamente.');
    }

    private function patientRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'second_last_name' => ['nullable', 'string', 'max:80'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'sex' => ['nullable', 'in:female,male,other,unspecified'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'address_line' => ['nullable', 'string', 'max:500'],
            'administrative_notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function patientPayload(array $validated): array
    {
        return [
            'first_name' => trim($validated['first_name']),
            'middle_name' => $this->nullableTrim($validated['middle_name'] ?? null),
            'last_name' => trim($validated['last_name']),
            'second_last_name' => $this->nullableTrim($validated['second_last_name'] ?? null),
            'date_of_birth' => $validated['date_of_birth'],
            'sex' => $validated['sex'] ?? null,
            'phone' => $this->nullableTrim($validated['phone'] ?? null),
            'email' => $this->nullableTrim($validated['email'] ?? null),
            'address_line' => $this->nullableTrim($validated['address_line'] ?? null),
            'administrative_notes' => $this->nullableTrim($validated['administrative_notes'] ?? null),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];
    }

    private function guardianRules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'second_last_name' => ['nullable', 'string', 'max:80'],
            'phone' => ['required', 'string', 'max:30'],
            'secondary_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:160'],
            'administrative_notes' => ['nullable', 'string', 'max:2000'],
            'relationship' => ['nullable', 'string', 'max:80'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }

    private function guardianPayload(array $validated): array
    {
        return [
            'first_name' => trim($validated['first_name']),
            'middle_name' => $this->nullableTrim($validated['middle_name'] ?? null),
            'last_name' => trim($validated['last_name']),
            'second_last_name' => $this->nullableTrim($validated['second_last_name'] ?? null),
            'phone' => trim($validated['phone']),
            'secondary_phone' => $this->nullableTrim($validated['secondary_phone'] ?? null),
            'email' => $this->nullableTrim($validated['email'] ?? null),
            'administrative_notes' => $this->nullableTrim($validated['administrative_notes'] ?? null),
        ];
    }

    private function patientAuditSnapshot(Patient $patient): array
    {
        return [
            'folio' => $patient->folio,
            'name' => $patient->full_name,
            'date_of_birth' => optional($patient->date_of_birth)->format('Y-m-d'),
            'sex' => $patient->sex,
            'is_active' => $patient->is_active,
        ];
    }

    private function guardianAuditSnapshot(Patient $patient, Guardian $guardian): array
    {
        $linked = $patient->guardians->firstWhere('id', $guardian->id);

        return [
            'name' => $guardian->full_name,
            'phone' => $guardian->phone,
            'email' => $guardian->email,
            'relationship' => $linked?->pivot?->relationship,
            'is_primary' => (bool) ($linked?->pivot?->is_primary ?? false),
        ];
    }

    private function ensureGuardianLinked(Patient $patient, Guardian $guardian): void
    {
        abort_unless(
            $patient->guardians()->where('guardians.id', $guardian->id)->exists(),
            404,
        );
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }

    private function patientMessages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe contener un correo válido.',
            'date' => 'El campo :attribute debe contener una fecha válida.',
            'before_or_equal' => 'La fecha de nacimiento no puede estar en el futuro.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
        ];
    }

    private function patientAttributes(): array
    {
        return [
            'first_name' => 'nombre',
            'middle_name' => 'segundo nombre',
            'last_name' => 'apellido paterno',
            'second_last_name' => 'apellido materno',
            'date_of_birth' => 'fecha de nacimiento',
            'sex' => 'sexo',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'address_line' => 'dirección',
            'administrative_notes' => 'notas administrativas',
            'is_active' => 'estado activo',
        ];
    }

    private function guardianMessages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe contener un correo válido.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'boolean' => 'El valor seleccionado para :attribute no es válido.',
        ];
    }

    private function guardianAttributes(): array
    {
        return [
            'first_name' => 'nombre del responsable',
            'middle_name' => 'segundo nombre',
            'last_name' => 'apellido paterno',
            'second_last_name' => 'apellido materno',
            'phone' => 'teléfono principal',
            'secondary_phone' => 'teléfono secundario',
            'email' => 'correo electrónico',
            'administrative_notes' => 'notas administrativas',
            'relationship' => 'parentesco o relación',
            'is_primary' => 'responsable principal',
        ];
    }
}
