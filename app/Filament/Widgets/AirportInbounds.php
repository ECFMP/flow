<?php

namespace App\Filament\Widgets;

use App\Models\Airport;
use App\Models\VatsimPilot;
use App\Models\VatsimPilotStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AirportInbounds extends BaseWidget
{

    private ?int $airportId = null;

    public function mount(int $airportId)
    {
        $this->airportId = $airportId;
        $this->records = VatsimPilot::where('estimated_arrival_time', '<', Carbon::now()->addMinutes(60))
            ->where('destination_airport', Airport::findOrFail($this->airportId)->icao_code)
            ->orderBy('estimated_arrival_time')
            ->get();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('callsign'),
            TextColumn::make('departure_airport'),
            TextColumn::make('vatsim_pilot_status_id')
                ->formatStateUsing(fn (VatsimPilot $record) => $record->vatsim_pilot_status_id->name),
            TextColumn::make('distance_to_destination')
                ->formatStateUsing(
                    fn(VatsimPilot $record) => $record->vatsim_pilot_status_id === VatsimPilotStatus::Landed ? '--' : $record->distance_to_destination
                ),
                TextColumn::make('estimated_arrival_time')
        ];
    }
}
