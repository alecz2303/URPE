<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HineAssessment extends Model
{
    protected $fillable = [
        'clinical_assessment_id', 'gestational_age', 'chronological_age',
        'corrected_age', 'head_circumference', 'cranial_nerves_score',
        'posture_score', 'movements_score', 'tone_score', 'reflexes_reactions_score',
        'global_score', 'asymmetry_count', 'general_comments',
    ];

    public function clinicalAssessment(): BelongsTo { return $this->belongsTo(ClinicalAssessment::class); }
    public function responses(): HasMany { return $this->hasMany(HineResponse::class); }

    protected function casts(): array
    {
        return [
            'cranial_nerves_score' => 'decimal:1', 'posture_score' => 'decimal:1',
            'movements_score' => 'decimal:1', 'tone_score' => 'decimal:1',
            'reflexes_reactions_score' => 'decimal:1', 'global_score' => 'decimal:1',
        ];
    }
}
