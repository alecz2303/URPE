<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClinicalAssessment extends Model
{
    public const TYPE_HINE = 'hine';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_FINALIZED = 'finalized';

    protected $fillable = [
        'patient_id', 'type', 'status', 'examination_date', 'authored_by_user_id',
        'finalized_at', 'instrument_version', 'metadata',
    ];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'authored_by_user_id'); }
    public function hine(): HasOne { return $this->hasOne(HineAssessment::class); }

    protected function casts(): array
    {
        return ['examination_date' => 'date', 'finalized_at' => 'datetime', 'metadata' => 'array'];
    }
}
