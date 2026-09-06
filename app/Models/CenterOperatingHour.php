<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterOperatingHour extends Model
{
    protected $fillable = [
        'day_of_week',
        'is_enabled',
        'opens_at',
        'closes_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
