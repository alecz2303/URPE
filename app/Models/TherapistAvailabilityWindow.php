<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistAvailabilityWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'is_enabled',
        'starts_at',
        'ends_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}
