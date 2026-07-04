<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionVoteAdjustment extends Model
{
    protected $fillable = [
        'academic_session_id',
        'position_id',
        'aspirant_id',
        'admin_id',
        'adjustment',
        'before_total',
        'after_total',
        'reason',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'adjustment' => 'integer',
            'before_total' => 'integer',
            'after_total' => 'integer',
        ];
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

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
