<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Services\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalRecordController extends Controller
{
    public function show(Patient $patient): View
    {
        $this->authorize('clinical_records.view');

        $patient->load(['clinicalRecord.creator', 'clinicalRecord.updater']);

        return view('clinical-records.show', [
            'patient' => $patient,
            'clinicalRecord' => $patient->clinicalRecord,
        ]);
    }

    public function update(Request $request, Patient $patient, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('clinical_records.manage');

        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());
        $record = $patient->clinicalRecord;
        $payload = $this->payload($validated);
        $userId = $request->user()?->getKey();

        if ($record === null) {
            $record = $patient->clinicalRecord()->create([
                ...$payload,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $audit->record('clinical_record.created', $record, [
                'patient_id' => $patient->getKey(),
                'patient_folio' => $patient->folio,
                'sections_with_content' => $this->sectionsWithContent($record),
            ], $request->user(), $request);

            return redirect()->route('clinical-records.show', $patient)
                ->with('status', 'Expediente clínico creado correctamente.');
        }

        $changedSections = collect($payload)
            ->filter(fn ($value, $key) => $record->getAttribute($key) !== $value)
            ->keys()
            ->values()
            ->all();

        $record->update([
            ...$payload,
            'updated_by' => $userId,
        ]);

        $audit->record('clinical_record.updated', $record, [
            'patient_id' => $patient->getKey(),
            'patient_folio' => $patient->folio,
            'changed_sections' => $changedSections,
            'sections_with_content' => $this->sectionsWithContent($record->refresh()),
        ], $request->user(), $request);

        return redirect()->route('clinical-records.show', $patient)
            ->with('status', 'Expediente clínico actualizado correctamente.');
    }

    private function rules(): array
    {
        return [
            'medical_history' => ['nullable', 'string', 'max:10000'],
            'prenatal_perinatal_history' => ['nullable', 'string', 'max:10000'],
            'developmental_history' => ['nullable', 'string', 'max:10000'],
            'family_history' => ['nullable', 'string', 'max:10000'],
            'diagnoses' => ['nullable', 'string', 'max:10000'],
            'therapeutic_objectives' => ['nullable', 'string', 'max:10000'],
            'general_observations' => ['nullable', 'string', 'max:10000'],
        ];
    }

    private function payload(array $validated): array
    {
        return collect(array_keys($this->rules()))
            ->mapWithKeys(fn (string $field) => [$field => $this->nullableTrim($validated[$field] ?? null)])
            ->all();
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }

    private function sectionsWithContent(ClinicalRecord $record): array
    {
        return collect(array_keys($this->rules()))
            ->filter(fn (string $field) => filled($record->getAttribute($field)))
            ->values()
            ->all();
    }

    private function messages(): array
    {
        return [
            'string' => 'El campo :attribute debe contener texto válido.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
        ];
    }

    private function attributes(): array
    {
        return [
            'medical_history' => 'antecedentes médicos',
            'prenatal_perinatal_history' => 'antecedentes prenatales y perinatales',
            'developmental_history' => 'antecedentes del desarrollo',
            'family_history' => 'antecedentes familiares',
            'diagnoses' => 'diagnósticos',
            'therapeutic_objectives' => 'objetivos terapéuticos',
            'general_observations' => 'observaciones generales',
        ];
    }
}
