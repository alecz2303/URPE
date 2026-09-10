<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW = 'no_show';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'patient_id',
        'therapy_id',
        'appointment_series_id',
        'series_occurrence',
        'starts_at',
        'ends_at',
        'duration_minutes',
        'status',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $attributes = [
        'status' => self::STATUS_SCHEDULED,
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'duration_minutes' => 'integer',
            'series_occurrence' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Programada',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_IN_PROGRESS => 'En atención',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_NO_SHOW => 'No asistió',
            self::STATUS_CANCELLED => 'Cancelada',
        ];
    }

    public static function transitionTargets(string $status): array
    {
        return match ($status) {
            self::STATUS_SCHEDULED => [self::STATUS_CONFIRMED, self::STATUS_IN_PROGRESS, self::STATUS_NO_SHOW],
            self::STATUS_CONFIRMED => [self::STATUS_SCHEDULED, self::STATUS_IN_PROGRESS, self::STATUS_NO_SHOW],
            self::STATUS_IN_PROGRESS => [self::STATUS_COMPLETED],
            default => [],
        };
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapy(): BelongsTo
    {
        return $this->belongsTo(Therapy::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(AppointmentSeries::class, 'appointment_series_id');
    }

    public function therapists(): BelongsToMany
    {
        return $this->belongsToMany(Therapist::class)->withTimestamps();
    }

    public function clinicalSessionLog(): HasOne
    {
        return $this->hasOne(ClinicalSessionLog::class);
    }

    public function therapistChanges(): HasMany
    {
        return $this->hasMany(AppointmentTherapistChange::class)->latest();
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_NO_SHOW, self::STATUS_CANCELLED], true);
    }

    public function allowsSessionCapture(): bool
    {
        return in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED, self::STATUS_IN_PROGRESS], true);
    }

    public function allowsScheduleChanges(): bool
    {
        return in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED], true);
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function isRecurring(): bool
    {
        return $this->appointment_series_id !== null;
    }
}
