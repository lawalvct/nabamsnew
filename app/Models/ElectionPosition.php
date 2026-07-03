<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionPosition extends Model
{
    protected $fillable = [
        'academic_session_id',
        'name',
        'form_amount',
        'admin_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'form_amount' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function aspirants(): BelongsToMany
    {
        return $this->belongsToMany(ElectionAspirant::class, 'election_aspirant_position')
            ->withPivot(['payment_status', 'result_status'])
            ->withTimestamps();
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ElectionVote::class, 'position_id');
    }
}
