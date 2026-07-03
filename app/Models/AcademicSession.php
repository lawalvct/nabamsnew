<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    protected $fillable = [
        'name',
        'starts_at_year',
        'ends_at_year',
        'is_current',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at_year' => 'integer',
            'ends_at_year' => 'integer',
        ];
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', 'Yes');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 'Yes');
    }
}
