<?php

namespace App\Helpers;

use App\Models\Airport;
use App\Models\VatsimPilot;
use App\Models\VatsimPilotStatus;
use Carbon\Carbon;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Cache;
use PDO;

class AirportStatistics
{
    public function getTotalInbound(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['total_inbound'];
    }

    public function getInbound15Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['inbound_15_minutes'];
    }

    public function getInbound30Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['inbound_30_minutes'];
    }

    public function getInbound60Minutes(int $airportId): int
    {
        return $this->getAirportStatistics($airportId)['inbound_60_minutes'];
    }

    private function getAirportStatistics(int $airportId): array
    {
        $airport = Airport::findOrFail($airportId);
        $allInbound = VatsimPilot::where('destination_airport', $airport->icao_code)->get();

        return Cache::remember(
            $this->buildCacheKey($airportId),
            Carbon::now()->addMinutes(5),
            fn() => [
                'total_inbound' => $allInbound->count(),
                'inbound_15_minutes' => $allInbound->where(fn(VatsimPilot $pilot) => $this->landingBefore($pilot, Carbon::now()->addMinutes(15)))
                    ->count(),
                'inbound_30_minutes' => $allInbound->where(fn(VatsimPilot $pilot) => $this->landingBefore($pilot, Carbon::now()->addMinutes(30)))
                    ->count(),
                'inbound_60_minutes' => $allInbound->where(fn(VatsimPilot $pilot) => $this->landingBefore($pilot, Carbon::now()->addMinutes(60)))
                    ->count(),
            ]
        );
    }

    private function landingBefore(VatsimPilot $pilot, Carbon $before): bool
    {
        return $pilot->estimated_arrival_time < $before && $pilot->vatsim_pilot_status_id !== VatsimPilotStatus::Landed;
    }

    private function buildCacheKey(int $airportId): string
    {
        return sprintf('AIRPORT_STATISTICS_%d', $airportId);
    }
}
