<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterSetting extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'timezone',
    ];
}
