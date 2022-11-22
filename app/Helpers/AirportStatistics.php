<?php

namespace App\Helpers;

use App\Models\Airport;
use App\Models\VatsimPilot;
use App\Models\VatsimPilotStatus;
use Carbon\Carbon;

class AirportStatistics
{
    private array $cache = [];

    public function getTotalInbound(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['total_inbound'];
    }

    public function getLandedLast10Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['landed_last_10_minutes'];
    }

    public function getInbound30Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['inbound_30_minutes'];
    }

    public function getInbound30To60Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['inbound_30_60_minutes'];
    }

    public function getInbound60To120Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['inbound_60_120_minutes'];
    }

    public function getAwaitingDeparture(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['awaiting_departure'];
    }

    public function getDepartingNearby(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['departing_nearby'];
    }

    private function getAirportStatistics(int $airportId): array
    {
        if (isset($this->cache[$airportId])) {
            return $this->cache[$airportId];
        }

        $airport = Airport::findOrFail($airportId);
        $allInbound = VatsimPilot::where('destination_airport', $airport->icao_code)->get();

        return $this->cache[$airportId] = [
            'total_inbound' => $allInbound->count(),
            'landed_last_10_minutes' => $allInbound->where(
                fn(VatsimPilot $pilot) => $pilot->vatsim_pilot_status_id === VatsimPilotStatus::Landed &&
                $pilot->estimated_arrival_time > Carbon::now()->subMinutes(10),
            )->count(),
            'inbound_30_minutes' => $allInbound->where(fn(VatsimPilot $pilot) => $this->landingBetween($pilot, Carbon::now(), Carbon::now()->addMinutes(30)))
                ->count(),
            'inbound_30_60_minutes' => $allInbound->where(fn(VatsimPilot $pilot) => $this->landingBetween($pilot, Carbon::now()->addMinutes(30), Carbon::now()->addMinutes(60)))
                ->count(),
            'inbound_60_120_minutes' => $allInbound->where(fn(VatsimPilot $pilot) => $this->landingBetween($pilot, Carbon::now()->addMinutes(60), Carbon::now()->addMinutes(120)))
                ->count(),
            'awaiting_departure' => $allInbound->where(fn(VatsimPilot $pilot) => $pilot->vatsim_pilot_status_id === VatsimPilotStatus::Ground)->count(),
            'departing_nearby' => $allInbound->where(fn(VatsimPilot $pilot) =>
                $pilot->vatsim_pilot_status_id === VatsimPilotStatus::Departing && $pilot->distance_to_destination < 400
            )->count(),
        ];
    }

    private function landingBetween(VatsimPilot $pilot, Carbon $from, Carbon $to): bool
    {
        return $pilot->vatsim_pilot_status_id !== VatsimPilotStatus::Landed &&
            $pilot->vatsim_pilot_status_id !== VatsimPilotStatus::Ground &&
            $pilot->estimated_arrival_time &&
                $pilot->estimated_arrival_time->gte($from) &&
                $pilot->estimated_arrival_time->lt($to);
    }
}
