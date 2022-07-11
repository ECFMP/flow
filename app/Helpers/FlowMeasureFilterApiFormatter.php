<?php

namespace App\Helpers;

use App\Models\AirportGroup;

class FlowMeasureFilterApiFormatter
{
    public static function formatAirportList(array $airports): array
    {
        return collect($airports)->map(function (int|string $airport) {
            if (is_string($airport)) {
                return $airport;
            }

            $group = AirportGroup::find($airport);
            return $group
                ? $group->airports()->pluck('icao_code')->toArray()
                : [];
        })->flatten()->unique()->sort()->values()->toArray();
    }
}
