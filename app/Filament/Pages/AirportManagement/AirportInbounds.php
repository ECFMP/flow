<?php

namespace App\Filament\Pages\AirportManagement;

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
        return '300s';
    }

    public function getTableQuery(): Builder
    {
        return VatsimPilot::where('estimated_arrival_time', '<', Carbon::now()->addMinutes(60))
            ->where('destination_airport', Airport::find($this->airportId)?->icao_code)
            ->where('estimated_arrival_time', '>', Carbon::now()->subMinutes(5))
            ->orderBy('estimated_arrival_time');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('callsign'),
            TextColumn::make('departure_airport'),
            TextColumn::make('vatsim_pilot_status_id')
                ->formatStateUsing(fn(VatsimPilot $record) => $record->vatsim_pilot_status_id->name),
            TextColumn::make('distance_to_destination')
                ->formatStateUsing(fn(VatsimPilot $record) => $record->vatsim_pilot_status_id === VatsimPilotStatus::Landed ? '--' : $record->distance_to_destination
                ),
            TextColumn::make('estimated_arrival_time')
        ];
    }
}
