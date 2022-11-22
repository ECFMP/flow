<?php

namespace App\Vatsim\Processor\Pilot;

use App\Helpers\ConvertsMetersToNauticalMiles;
use App\Models\Airport;
use Location\Coordinate;
use Location\Distance\Haversine;

trait CalculatesDistancesFromAirfields
{
    use ConvertsMetersToNauticalMiles;

    private function distanceFromAirfield(array $data, Airport $airport): float
    {
        return $this->metersToNauticalMiles(
                (new Coordinate($data['latitude'], $data['longitude']))
                ->getDistance($airport->getCoordinate(), new Haversine())
        );
    }
}
