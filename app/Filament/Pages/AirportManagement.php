<?php

namespace App\Filament\Pages;

use App\Models\Airport;
use Filament\Pages\Page;

class AirportManagement extends Page
{
    public ?int $airportId = null;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string $view = 'filament.pages.airport-management';

    public function updatedAirportId()
    {
        $this->emit('airportIdUpdated', $this->airportId);
    }

    public function getViewData(): array
    {
        return [
            'airports' => Airport::orderBy('icao_code')
                ->get()
                ->mapWithKeys(fn(Airport $airport) => [$airport->id => $airport->icao_code])
                ->toArray(),
            'airport' => Airport::find($this->airportId)?->toArray(),
        ];
    }
}
