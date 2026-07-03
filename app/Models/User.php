<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'nickname',
        'gender',
        'email',
        'password',
        'matno',
        'phone',
        'whatsapp_number',
        'home_address',
        'department',
        'academic_level',
        'level_id',
        'member_type',
        'expectation_msg',
        'session_start',
        'session_end',
        'is_active',
        'is_ban',
        'fee_paid',
        'role',
        'bio',
        'dob',
        'image',
        'facebook_link',
        'x_link',
        'linkedin_link',
        'email_verified_at',
        'user_role_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim($this->firstname.' '.$this->lastname);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        if (str_starts_with($this->image, 'profile_photos/') || str_starts_with($this->image, 'passport_photographs/')) {
            return route('profile.photo', [
                'directory' => dirname($this->image),
                'filename' => basename($this->image),
            ]);
        }

        if (str_starts_with($this->image, 'uploads/')) {
            return asset($this->image);
        }

        return Storage::disk('public')->url($this->image);
    }
}
