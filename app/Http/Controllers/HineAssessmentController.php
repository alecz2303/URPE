<?php

namespace App\Http\Controllers;

use App\Models\ClinicalAssessment;
use App\Models\HineAssessment;
use App\Models\HineResponse;
use App\Models\Patient;
use App\Services\AuditTrail;
use App\Services\HineScoreCalculator;
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
    public function edit(Request $request, Patient $patient, ClinicalAssessment $assessment): View
    {
        abort_unless($request->user()->hasPermission('clinical_assessments.manage'), 403);
        $this->assertHineForPatient($patient, $assessment);
        abort_if($assessment->status === ClinicalAssessment::STATUS_FINALIZED, 403, 'La evaluación HINE finalizada está cerrada para edición.');

        $assessment->load('hine.responses');

        return view('clinical-assessments.hine.edit', [
            'patient' => $patient,
            'assessment' => $assessment,
            'hine' => $assessment->hine,
            'sections' => HineInstrument::neurologicalSections(),
            'scores' => HineInstrument::URPE_SCORES,
            'anchors' => HineInstrument::clinicalAnchors(),
            'visuals' => HineInstrument::visualReferenceMap(),
            'responses' => $assessment->hine->responses->keyBy('item_key'),
        ]);
    }

    public function update(Request $request, Patient $patient, ClinicalAssessment $assessment, AuditTrail $audit, HineScoreCalculator $calculator): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('clinical_assessments.manage'), 403);
        $this->assertHineForPatient($patient, $assessment);
        abort_if($assessment->status === ClinicalAssessment::STATUS_FINALIZED, 403, 'La evaluación HINE finalizada está cerrada para edición.');

        $catalog = collect(HineInstrument::neurologicalSections())
            ->flatMap(fn (array $section, string $sectionKey) => collect($section['items'])->mapWithKeys(
                fn (array $item) => [$item['key'] => ['section' => $sectionKey, 'laterality' => (bool) ($item['laterality'] ?? false)]]
            ));

        $data = $request->validate([
            'responses' => ['nullable', 'array'],
            'responses.*.score' => ['nullable', 'numeric', 'in:0,0.5,1,1.5,2,2.5,3'],
            'responses.*.asymmetry' => ['nullable', 'boolean'],
            'responses.*.comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $submitted = collect($data['responses'] ?? [])->only($catalog->keys()->all());

        DB::transaction(function () use ($request, $assessment, $submitted, $catalog, $calculator, $audit): void {
            foreach ($submitted as $itemKey => $response) {
                $definition = $catalog[$itemKey];

                HineResponse::query()->updateOrCreate(
                    ['hine_assessment_id' => $assessment->hine->id, 'item_key' => $itemKey],
                    [
                        'section_key' => $definition['section'],
                        'score' => array_key_exists('score', $response) && $response['score'] !== null ? $response['score'] : null,
                        'asymmetry' => $definition['laterality'] ? (bool) ($response['asymmetry'] ?? false) : null,
                        'comments' => $response['comments'] ?? null,
                    ],
                );
            }

            $assessment->hine->load('responses');
            $calculated = $calculator->calculate($assessment->hine->responses->map(fn (HineResponse $response) => [
                'section_key' => $response->section_key,
                'score' => $response->score,
                'asymmetry' => $response->asymmetry,
            ])->all());

            $assessment->hine->update([
                'cranial_nerves_score' => $calculated['subtotals']['cranial_nerves'],
                'posture_score' => $calculated['subtotals']['posture'],
                'movements_score' => $calculated['subtotals']['movements'],
                'tone_score' => $calculated['subtotals']['tone'],
                'reflexes_reactions_score' => $calculated['subtotals']['reflexes_reactions'],
                'global_score' => $calculated['global_score'],
                'asymmetry_count' => $calculated['asymmetry_count'],
            ]);

            $audit->record('clinical_assessment.draft_updated', $assessment, [
                'patient_id' => $assessment->patient_id,
                'type' => ClinicalAssessment::TYPE_HINE,
                'status' => ClinicalAssessment::STATUS_DRAFT,
                'scored_items' => $assessment->hine->responses->whereNotNull('score')->count(),
                'clinical_content_stored_in_audit' => false,
            ], $request->user(), $request);
        });

        return redirect()
            ->route('patients.hine-assessments.edit', [$patient, $assessment])
            ->with('success', 'Borrador HINE guardado correctamente.');
    }

    private function assertHineForPatient(Patient $patient, ClinicalAssessment $assessment): void
    {
        abort_unless(
            $assessment->patient_id === $patient->id && $assessment->type === ClinicalAssessment::TYPE_HINE,
            404
        );
    }

}
