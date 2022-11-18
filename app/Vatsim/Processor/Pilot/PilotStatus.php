<?php

namespace App\Vatsim\Processor\Pilot;

use App\Models\Airport;
use App\Models\VatsimPilotStatus;

class PilotStatus implements PilotDataSubprocessorInterface
{
    use CalculatesDistancesFromAirfields;
    use ChecksFlightplanFields;

    // In knots
    private const MAX_LANDED_GROUNDSPEED = 50;
    private const MAX_GROUND_GROUNDSPEED = 50;
    private const MAX_GROUND_GROUNDSPEED_NO_DEPARTURE_AIRPORT = 30;

    // In nautical miles
    private const MAX_LANDED_DISTANCE_FROM_AIRPORT = 3;
    private const MAX_GROUND_DISTANCE_FROM_AIRPORT = 3;
    private const MAX_DESCENDING_DISTANCE_FROM_AIRPORT = 150;

    // In feet
    private const MAX_CRUISE_LEVEL_DEVIATION = 4000;

    public function processPilotData(array $data): array
    {
        return ['vatsim_pilot_status_id' => $this->generatePilotStatus($data)];
    }

    private function generatePilotStatus(array $data): VatsimPilotStatus
    {
        if ($this->hasArrived($data)) {
            return VatsimPilotStatus::Landed;
        }

        if ($this->isDescending($data)) {
            return VatsimPilotStatus::Descending;
        }

        if ($this->isOnTheGround($data)) {
            return VatsimPilotStatus::Ground;
        }

        return $this->isCruising($data)
            ? VatsimPilotStatus::Cruise
            : VatsimPilotStatus::Departing;
    }

    private function hasArrived(array $data): bool
    {
        if ($data['groundspeed'] >= self::MAX_LANDED_GROUNDSPEED) {
            return false;
        }

        if (!$this->hasFlightplan($data)) {
            return false;
        }

        if (is_null($arrivalAirport = $this->getAirport($this->getFlightplanItem($data, 'arrival')))) {
            return false;
        }

        return $this->aircraftWithinDistanceOfAirport($data, $arrivalAirport, self::MAX_LANDED_DISTANCE_FROM_AIRPORT);
    }

    private function isDescending(array $data): bool
    {
        if (!$this->hasFlightplan($data)) {
            return false;
        }

        if (is_null($arrivalAirport = $this->getAirport($this->getFlightplanItem($data, 'arrival')))) {
            return false;
        }

        return $this->aircraftWithinDistanceOfAirport($data, $arrivalAirport, self::MAX_DESCENDING_DISTANCE_FROM_AIRPORT) &&
            !$this->isCruising($data);
    }

    private function isOnTheGround(array $data): bool
    {
        if (!$this->hasFlightplan($data) || is_null($departureAirport = $this->getAirport($this->getFlightplanItem($data, 'departure')))) {
            return $data['groundspeed'] < self::MAX_GROUND_GROUNDSPEED_NO_DEPARTURE_AIRPORT;
        }

        return $data['groundspeed'] < self::MAX_GROUND_GROUNDSPEED &&
            $this->aircraftWithinDistanceOfAirport($data, $departureAirport, self::MAX_GROUND_DISTANCE_FROM_AIRPORT);
    }

    private function isCruising(array $data): bool
    {
        $cruiseLevel = $this->getFlightplanItem($data, 'altitude');

        return $cruiseLevel && abs((int) $cruiseLevel - (int) $data['altitude']) < self::MAX_CRUISE_LEVEL_DEVIATION;
    }

    private function getAirport(string $icao): ? Airport
    {
        return Airport::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('icao_code', $icao)
            ->first();
    }

    private function aircraftWithinDistanceOfAirport(array $data, Airport $airport, int $nauticalMiles): bool
    {
        return $this->distanceFromAirfield($data, $airport) < $nauticalMiles;
    }
}
