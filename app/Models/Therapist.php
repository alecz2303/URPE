<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Therapist extends Model
{
    use HasFactory;

    protected $attributes = [
        'is_active' => true,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'professional_title',
        'license_number',
        'phone',
        'email',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availabilityWindows(): HasMany
    {
        return $this->hasMany(TherapistAvailabilityWindow::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(TherapistBlock::class);
    }

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class)->withTimestamps();
    }
}
