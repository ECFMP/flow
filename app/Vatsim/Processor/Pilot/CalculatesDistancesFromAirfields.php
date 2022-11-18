<?php

namespace App\Vatsim\Processor\Pilot;

use App\Models\Airport;
use Location\Coordinate;
use Location\Distance\Haversine;

trait CalculatesDistancesFromAirfields
{
    private function distanceFromAirfield(array $data, Airport $airport): float
    {
        return $this->metersToNauticalMiles(
                (new Coordinate($data['latitude'], $data['longitude']))
                ->getDistance(new Coordinate($airport->latitude, $airport->longitude), new Haversine())
        );
    }

    private function metersToNauticalMiles(float $meters): float
    {
        return $meters * 0.000539957;
    }
}
