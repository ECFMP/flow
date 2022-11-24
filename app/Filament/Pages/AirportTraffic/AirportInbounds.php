<?php

namespace App\Filament\Pages\AirportTraffic;

use App\Models\Airport;
use App\Models\VatsimPilot;
use App\Models\VatsimPilotStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AirportInbounds extends BaseWidget
{
    use CanPoll;

    public ?int $airportId = null;

    protected $listeners = ['airportIdUpdated'];

    public function mount(int $airportId)
    {
        $this->airportId = $airportId;
    }

    public function airportIdUpdated(int $airportId)
    {
        $this->airportId = $airportId;
    }

    protected function getTablePollingInterval(): ?string
    {
        return '60s';
    }

    public function getTableQuery(): Builder
    {
        return VatsimPilot::where('destination_airport', Airport::find($this->airportId)?->icao_code)
            ->where('estimated_arrival_time', '>', Carbon::now()->subMinutes(5))
            ->orderBy('estimated_arrival_time');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('callsign')
                ->label('Callsign'),
            TextColumn::make('departure_airport')
                ->label('Departure Airport'),
            TextColumn::make('vatsim_pilot_status_id')
                ->label('Status')
                ->formatStateUsing(fn (VatsimPilot $record) => $record->vatsim_pilot_status_id->name),
            TextColumn::make('distance_to_destination')
                ->label('Distance to Destination (NM)')
                ->formatStateUsing(
                    fn (VatsimPilot $record) =>
                        $record->vatsim_pilot_status_id === VatsimPilotStatus::Landed
                            ? '--'
                            : round($record->distance_to_destination),
                ),
            TextColumn::make('estimated_arrival_time')
                ->label('Estimated Arrival Time')
        ];
    }
}
