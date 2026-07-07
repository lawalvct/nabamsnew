<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'value',
        'setting_group',
        'active',
    ];

    public static function election(): self
    {
        return self::query()->firstOrCreate(
            ['slug' => 'election'],
            [
                'name' => 'Election',
                'value' => 'On',
                'setting_group' => 'general',
                'active' => 'Yes',
            ],
        );
    }

    public static function electionEnabled(): bool
    {
        $setting = self::query()->where('slug', 'election')->first();

        if (! $setting || $setting->active !== 'Yes') {
            return false;
        }

        return in_array(strtolower((string) $setting->value), ['on', 'yes', '1', 'true', 'enabled'], true);
    }

    public static function registration(): self
    {
        return self::query()->firstOrCreate(
            ['slug' => 'registration'],
            [
                'name' => 'Member Registration',
                'value' => 'On',
                'setting_group' => 'general',
                'active' => 'Yes',
            ],
        );
    }

    public static function registrationEnabled(): bool
    {
        $setting = self::query()->where('slug', 'registration')->first();

        if (! $setting) {
            return true;
        }

        if ($setting->active !== 'Yes') {
            return false;
        }

        return in_array(strtolower((string) $setting->value), ['on', 'yes', '1', 'true', 'enabled'], true);
    }
}
