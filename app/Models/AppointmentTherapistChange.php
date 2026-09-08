<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentTherapistChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'removed_therapist_id',
        'added_therapist_id',
        'changed_by_user_id',
        'reason',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function removedTherapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class, 'removed_therapist_id');
    }

    public function addedTherapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class, 'added_therapist_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
