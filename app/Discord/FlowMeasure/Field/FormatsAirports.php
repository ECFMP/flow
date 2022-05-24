<?php

namespace App\Discord\FlowMeasure\Field;

use App\Models\AirportGroup;

trait FormatsAirports
{
    protected function formatAirports(array $airports)
    {
        return collect($airports)->map(function (int|string $airport) {
            if (is_string($airport)) {
                return $airport;
            }

            $group = AirportGroup::with('airports')->find($airport);
            return $group
                ? sprintf('%s [%s]', $group->name, $group->airports->pluck('icao_code')->join(', '))
                : '';
        })->unique()->sort()->values()->join(', ');
    }
}
