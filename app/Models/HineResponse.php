<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HineResponse extends Model
{
    protected $fillable = [
        'hine_assessment_id', 'section_key', 'item_key', 'score',
        'asymmetry', 'response_data', 'comments',
    ];

    public function hineAssessment(): BelongsTo { return $this->belongsTo(HineAssessment::class); }

    protected function casts(): array
    {
        return ['score' => 'decimal:1', 'asymmetry' => 'boolean', 'response_data' => 'array'];
    }
}
