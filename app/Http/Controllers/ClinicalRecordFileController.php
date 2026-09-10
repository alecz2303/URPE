<?php

namespace App\Http\Controllers;

use App\Models\ClinicalFile;
use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Services\ClinicalFileStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClinicalRecordFileController extends Controller
{
    public function store(Request $request, Patient $patient, ClinicalFileStorage $storage): RedirectResponse
    {
        $this->authorize('clinical_records.manage');

        $clinicalRecord = $patient->clinicalRecord;
        abort_unless($clinicalRecord instanceof ClinicalRecord, 404);

        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:20480'],
            'category' => ['required', Rule::in(['document', 'image', 'radiograph', 'study', 'other'])],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'file.required' => 'Selecciona un archivo clínico.',
            'file.file' => 'El archivo clínico no es válido.',
            'file.mimes' => 'El archivo debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'file.max' => 'El archivo no debe exceder 20 MB.',
            'category.required' => 'Selecciona el tipo de archivo clínico.',
            'category.in' => 'El tipo de archivo clínico no es válido.',
            'description.max' => 'La descripción no debe exceder 500 caracteres.',
        ]);

        $storage->store(
            $validated['file'],
            $request->user(),
            $clinicalRecord,
            [
                'category' => $validated['category'],
                'description' => filled($validated['description'] ?? null)
                    ? trim($validated['description'])
                    : null,
            ],
        );

        return redirect()->route('clinical-records.show', $patient)
            ->with('status', 'Archivo clínico cargado correctamente.');
    }

    public function destroy(
        Request $request,
        Patient $patient,
        ClinicalFile $clinicalFile,
        ClinicalFileStorage $storage,
    ): RedirectResponse {
        $this->authorize('clinical_records.manage');

        $clinicalRecord = $patient->clinicalRecord;
        abort_unless($clinicalRecord instanceof ClinicalRecord, 404);
        abort_unless(
            $clinicalFile->subject_type === $clinicalRecord->getMorphClass()
            && (string) $clinicalFile->subject_id === (string) $clinicalRecord->getKey(),
            404,
        );

        $storage->retire($clinicalFile, $request->user());

        return redirect()->route('clinical-records.show', $patient)
            ->with('status', 'Archivo clínico retirado del expediente.');
    }
}
