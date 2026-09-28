<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * Seeded weighted shuffle for the public member wall.
 *
 * Every member can land anywhere, but recently joined members get a higher
 * weight so they tend to surface near the top. The weight decays smoothly:
 * roughly 8x on the day they join, ~3.6x after 30 days and ~1.3x after 90.
 */
class MemberWallOrder
{
    public const NEW_MEMBER_DAYS = 30;

    private const MAX_BOOST = 7;

    private const BOOST_DECAY_DAYS = 30;

    /**
     * @param  Collection<int, object{id: int, created_at: mixed}>  $members
     * @return array<int, int> member ids in display order
     */
    public static function ids(Collection $members, int $seed): array
    {
        $randomizer = new Randomizer(new Mt19937($seed));
        $now = Carbon::now();

        return $members
            ->sortBy('id')
            ->map(function (object $member) use ($randomizer, $now): array {
                // Efraimidis-Spirakis weighted sampling: smaller key sorts first.
                $random = max($randomizer->nextFloat(), PHP_FLOAT_MIN);

                return [
                    'id' => (int) $member->id,
                    'key' => -log($random) / self::weight($member->created_at, $now),
                ];
            })
            ->sortBy('key')
            ->pluck('id')
            ->values()
            ->all();
    }

    public static function weight(mixed $joinedAt, ?Carbon $now = null): float
    {
        if (! $joinedAt) {
            return 1.0;
        }

        $ageInDays = max(0, Carbon::parse($joinedAt)->diffInDays($now ?? Carbon::now()));

        return 1 + self::MAX_BOOST * exp(-$ageInDays / self::BOOST_DECAY_DAYS);
    }

    public static function isNew(mixed $joinedAt): bool
    {
        return $joinedAt && Carbon::parse($joinedAt)->gt(Carbon::now()->subDays(self::NEW_MEMBER_DAYS));
    }
}
