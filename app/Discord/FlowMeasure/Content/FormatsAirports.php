<?php

namespace App\Discord\FlowMeasure\Content;

use App\Models\AirportGroup;

trait FormatsAirports
{
    protected function formatAirports(array $airports)
    {
        return collect($airports)->map(function (int|string $airport) {
            if (is_string($airport)) {
                return $airport;
            }

            $group = AirportGroup::find($airport);
            return $group
                ? [$group->name]
                : [];
        })->flatten()->unique()->sort()->values()->join(', ');
    }
}
