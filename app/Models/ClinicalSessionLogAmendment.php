<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalSessionLogAmendment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_session_log_id',
        'authored_by_user_id',
        'reason',
        'content',
    ];

    public function clinicalSessionLog(): BelongsTo
    {
        return $this->belongsTo(ClinicalSessionLog::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authored_by_user_id');
    }
}
