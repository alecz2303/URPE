<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HineAssessment extends Model
{
    protected $fillable = [
        'clinical_assessment_id', 'gestational_age_weeks', 'chronological_age_days',
        'corrected_age_days', 'head_circumference_cm', 'cranial_nerves_score',
        'posture_score', 'movements_score', 'tone_score', 'reflexes_reactions_score',
        'global_score', 'asymmetry_count', 'behavior_score', 'general_comments',
    ];

    public function clinicalAssessment(): BelongsTo { return $this->belongsTo(ClinicalAssessment::class); }
    public function responses(): HasMany { return $this->hasMany(HineResponse::class); }

    protected function casts(): array
    {
        return [
            'gestational_age_weeks' => 'decimal:1', 'head_circumference_cm' => 'decimal:2',
            'cranial_nerves_score' => 'decimal:1', 'posture_score' => 'decimal:1',
            'movements_score' => 'decimal:1', 'tone_score' => 'decimal:1',
            'reflexes_reactions_score' => 'decimal:1', 'global_score' => 'decimal:1',
            'behavior_score' => 'decimal:1',
        ];
    }
}
