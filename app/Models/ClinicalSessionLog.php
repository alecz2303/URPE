<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalSessionLog extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'therapy_id',
        'authored_by_user_id',
        'status',
        'treatment_activities',
        'patient_response',
        'observations_incidents',
        'home_recommendations',
        'next_session_objectives',
        'completed_at',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapy(): BelongsTo
    {
        return $this->belongsTo(Therapy::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authored_by_user_id');
    }

    public function participatingTherapists(): BelongsToMany
    {
        return $this->belongsToMany(Therapist::class, 'clinical_session_log_therapist')->withTimestamps();
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(ClinicalSessionLogAmendment::class)->oldest('created_at')->oldest('id');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
