<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'second_last_name',
        'phone',
        'secondary_phone',
        'email',
        'administrative_notes',
    ];

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class)
            ->using(PatientGuardian::class)
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->second_last_name,
        ])->filter()->implode(' ');
    }
}
