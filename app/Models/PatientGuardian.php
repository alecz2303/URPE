<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientGuardian extends Pivot
{
    protected $table = 'guardian_patient';

    protected $fillable = [
        'patient_id',
        'guardian_id',
        'relationship',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
