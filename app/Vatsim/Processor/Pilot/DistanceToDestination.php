<?php

namespace App\Vatsim\Processor\Pilot;

use App\Models\Airport;

class DistanceToDestination implements PilotDataSubprocessorInterface
{
    use CalculatesDistancesFromAirfields;
    use ChecksFlightplanFields;

    public function processPilotData(array $data, array $transformedData): array
    {
        return ['distance_to_destination' => $this->calculateDistanceToDestination($data)];
    }

    private function calculateDistanceToDestination(array $data): ?float
    {
        $arrivalAirport = $this->getArrivalAirport($data);

        return $arrivalAirport
            ? $this->distanceFromAirfield($data, $arrivalAirport)
            : null;
    }

    private function getArrivalAirport(array $data): ?Airport
    {
        return Airport::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('icao_code', $this->getFlightplanItem($data, 'arrival'))
            ->first();
    }
}
