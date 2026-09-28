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

    public const PAYMENT_OFF = 'off';

    public const PAYMENT_SESSION = 'session';

    public const PAYMENT_SESSION_SEMESTER = 'session_semester';

    public const PAYMENT_REQUIREMENTS = [
        self::PAYMENT_OFF,
        self::PAYMENT_SESSION,
        self::PAYMENT_SESSION_SEMESTER,
    ];

    public static function paymentRequirement(): self
    {
        return self::query()->firstOrCreate(
            ['slug' => 'payment_requirement'],
            [
                'name' => 'Dashboard Payment Requirement',
                'value' => self::PAYMENT_SESSION_SEMESTER,
                'setting_group' => 'payments',
                'active' => 'Yes',
            ],
        );
    }

    public static function paymentRequirementMode(): string
    {
        $setting = self::query()->where('slug', 'payment_requirement')->first();

        if (! $setting) {
            return self::PAYMENT_SESSION_SEMESTER;
        }

        if ($setting->active !== 'Yes' || ! in_array($setting->value, self::PAYMENT_REQUIREMENTS, true)) {
            return self::PAYMENT_OFF;
        }

        return $setting->value;
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
