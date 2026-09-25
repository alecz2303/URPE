<?php

namespace App\Http\Controllers;

use App\Models\ClinicalAssessment;
use App\Models\HineAssessment;
use App\Models\Patient;
use App\Services\AuditTrail;
use App\Support\HineInstrument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HineAssessmentController extends Controller
{
    public function index(Request $request, Patient $patient): View
    {
        abort_unless($request->user()->hasPermission('clinical_assessments.view'), 403);

        $assessments = $patient->clinicalAssessments()
            ->where('type', ClinicalAssessment::TYPE_HINE)
            ->with(['author', 'hine'])
            ->orderByDesc('examination_date')
            ->orderByDesc('id')
            ->get();

        return view('clinical-assessments.hine.index', compact('patient', 'assessments'));
    }

    public function create(Request $request, Patient $patient): View
    {
        abort_unless($request->user()->hasPermission('clinical_assessments.manage'), 403);

        return view('clinical-assessments.hine.create', [
            'patient' => $patient,
            'instrumentVersion' => HineInstrument::VERSION,
        ]);
    }

    public function store(Request $request, Patient $patient, AuditTrail $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('clinical_assessments.manage'), 403);

        $data = $request->validate([
            'examination_date' => ['required', 'date'],
            'gestational_age' => ['nullable', 'string', 'max:100'],
            'chronological_age' => ['nullable', 'string', 'max:100'],
            'corrected_age' => ['nullable', 'string', 'max:100'],
            'head_circumference' => ['nullable', 'string', 'max:100'],
        ]);

        $assessment = DB::transaction(function () use ($request, $patient, $data, $audit): ClinicalAssessment {
            $assessment = ClinicalAssessment::query()->create([
                'patient_id' => $patient->id,
                'type' => ClinicalAssessment::TYPE_HINE,
                'status' => ClinicalAssessment::STATUS_DRAFT,
                'examination_date' => $data['examination_date'],
                'authored_by_user_id' => $request->user()->id,
                'instrument_version' => HineInstrument::VERSION,
            ]);

            HineAssessment::query()->create([
                'clinical_assessment_id' => $assessment->id,
                'gestational_age' => $data['gestational_age'] ?? null,
                'chronological_age' => $data['chronological_age'] ?? null,
                'corrected_age' => $data['corrected_age'] ?? null,
                'head_circumference' => $data['head_circumference'] ?? null,
            ]);

            $audit->record('clinical_assessment.created', $assessment, [
                'patient_id' => $patient->id,
                'type' => ClinicalAssessment::TYPE_HINE,
                'status' => ClinicalAssessment::STATUS_DRAFT,
                'instrument_version' => HineInstrument::VERSION,
                'clinical_content_stored_in_audit' => false,
            ], $request->user(), $request);

            return $assessment;
        });

        return redirect()
            ->route('patients.hine-assessments.index', $patient)
            ->with('success', 'Evaluación HINE creada como borrador.');
    }
}
