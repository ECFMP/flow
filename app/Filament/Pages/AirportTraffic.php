<?php

namespace App\Filament\Pages;

use App\Enums\RoleKey;
use App\Models\Airport;
use Filament\Pages\Page;

class AirportTraffic extends Page
{
    public ?int $airportId = null;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.airport-traffic';

    protected static ?string $title = 'Airport Traffic Insights';

    protected $listeners = ['airportIdUpdated'];

    protected function getTitle(): string
    {
        return sprintf(
            'Airport Traffic Insights%s',
            $this->airportId ? ' - ' . Airport::findOrFail($this->airportId)->icao_code : ''
        );
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return in_array(
            auth()->user()->role->key,
            [
                RoleKey::SYSTEM,
                RoleKey::NMT,
                RoleKey::EVENT_MANAGER,
                RoleKey::FLOW_MANAGER
            ]
        );
    }

    public function mount(): void
    {
        abort_unless(self::shouldRegisterNavigation(), 403);
    }

    public function airportIdUpdated(int $airportId)
    {
        $this->airportId = $airportId;
    }

    public function getViewData(): array
    {
        return [
            'airports' => Airport::orderBy('icao_code')
                ->get()
                ->mapWithKeys(fn (Airport $airport) => [$airport->id => $airport->icao_code])
                ->toArray(),
            'airport' => Airport::find($this->airportId)?->toArray(),
        ];
    }
}
