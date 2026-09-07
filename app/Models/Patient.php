<?php

namespace App\Models;

use App\Services\PatientFolioGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use HasFactory;

    protected $attributes = [
        'is_active' => true,
    ];

    protected $fillable = [
        'folio',
        'first_name',
        'middle_name',
        'last_name',
        'second_last_name',
        'date_of_birth',
        'sex',
        'phone',
        'email',
        'address_line',
        'administrative_notes',
        'is_active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Patient $patient): void {
            if (! $patient->folio) {
                $patient->folio = app(PatientFolioGenerator::class)->next();
            }
        });
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class)
            ->using(PatientGuardian::class)
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps();
    }

    public function clinicalRecord(): HasOne
    {
        return $this->hasOne(ClinicalRecord::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
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

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
