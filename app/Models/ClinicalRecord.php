<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_history',
        'prenatal_perinatal_history',
        'developmental_history',
        'family_history',
        'diagnoses',
        'therapeutic_objectives',
        'general_observations',
        'created_by',
        'updated_by',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
