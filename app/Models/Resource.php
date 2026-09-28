<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    public const CATEGORIES = [
        'Lecture Notes',
        'Past Questions',
        'Textbooks',
        'Projects & Research',
        'Templates',
        'Audio & Video',
        'Other',
    ];

    protected $fillable = [
        'title',
        'description',
        'category',
        'level_id',
        'access',
        'price',
        'file_path',
        'file_extension',
        'file_mime',
        'file_size',
        'is_published',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'file_size' => 'integer',
            'view_count' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', 'Yes');
    }

    public function isFree(): bool
    {
        return $this->access === 'free' || $this->price <= 0;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->payments()->approved()->where('user_id', $user->id)->exists();
    }

    public function canBeDownloadedBy(User $user): bool
    {
        if (strtolower((string) $user->role) === 'admin') {
            return true;
        }

        return $this->is_published === 'Yes' && ($this->isFree() || $this->isOwnedBy($user));
    }

    public function humanFileSize(): string
    {
        $size = $this->file_size;

        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($size < 1024 || $unit === 'GB') {
                return round($size, $unit === 'B' ? 0 : 1).' '.$unit;
            }

            $size /= 1024;
        }

        return $size.' B';
    }
}
