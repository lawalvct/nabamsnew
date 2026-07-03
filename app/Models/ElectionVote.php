<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionVote extends Model
{
    protected $fillable = [
        'user_id',
        'academic_session_id',
        'position_id',
        'aspirant_id',
        'ip_address',
    ];

    public function voter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(ElectionPosition::class, 'position_id');
    }

    public function aspirant(): BelongsTo
    {
        return $this->belongsTo(ElectionAspirant::class, 'aspirant_id');
    }
}
