<?php

namespace App\Vatsim\Processor\Pilot;

use App\Models\Airport;
use App\Models\VatsimPilot;
use App\Models\VatsimPilotStatus;
use Carbon\Carbon;
use LogicException;

class EstimatedArrivalTime implements PilotDataSubprocessorInterface
{
    use CalculatesDistancesFromAirfields;
    use ChecksFlightplanFields;

    public function processPilotData(array $data, array $transformedData): array
    {
        if (!isset($transformedData['vatsim_pilot_status_id'])) {
            throw new LogicException('Vatsim pilot status id not set');
        }

        if (is_null($this->getFlightplanItem($data, 'arrival')) || is_null($arrivalAirfield = $this->arrivalAirport($data))) {
            return [
                'estimated_arrival_time' => null
            ];
        }

        return [
            'estimated_arrival_time' => match ($transformedData['vatsim_pilot_status_id']) {
                VatsimPilotStatus::Ground => null,
                VatsimPilotStatus::Departing => $this->calculateTimeToAirport($this->getFlightplanItem($data, 'cruise_tas'), $data, $arrivalAirfield),
                VatsimPilotStatus::Cruise, VatsimPilotStatus::Descending => $this->calculateTimeToAirport($data['groundspeed'], $data, $arrivalAirfield),
                VatsimPilotStatus::Landed => $this->calculateLandingTime($data['callsign'])
            }
        ];
    }

    private function calculateTimeToAirport(int $groundspeed, array $data, Airport $airport): ?Carbon
    {
        if ($groundspeed === 0) {
            return null;
        }

        return Carbon::now()->addMinutes((int) (($this->distanceFromAirfield($data, $airport) / $groundspeed) * 60));
    }

    private function calculateLandingTime(string $callsign): Carbon
    {
        $existing = VatsimPilot::where('callsign', $callsign)->first();
        return $existing && $existing->vatsim_pilot_status_id === VatsimPilotStatus::Landed
            ? $existing->estimated_arrival_time
            : Carbon::now();
    }

    private function arrivalAirport(array $data): ?Airport
    {
        return Airport::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('icao_code', $this->getFlightplanItem($data, 'arrival'))
            ->first();
    }
}
