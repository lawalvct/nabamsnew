<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ElectionAspirant extends Model
{
    protected $fillable = [
        'user_id',
        'academic_session_id',
        'name',
        'manifesto',
        'photo',
        'screening_status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function positions(): BelongsToMany
    {
        return $this->belongsToMany(ElectionPosition::class, 'election_aspirant_position')
            ->withPivot(['payment_status', 'result_status'])
            ->withTimestamps();
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ElectionVote::class, 'aspirant_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return $this->user?->image_url;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        if (str_starts_with($this->photo, 'storage/') || str_starts_with($this->photo, 'uploads/')) {
            return asset($this->photo);
        }

        return Storage::disk('public')->url($this->photo);
    }
}
