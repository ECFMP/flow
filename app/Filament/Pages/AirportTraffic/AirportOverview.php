<?php

namespace App\Filament\Pages\AirportTraffic;

use App\Helpers\AirportStatistics;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AirportOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';

    public $airportId;

    protected $listeners = ['airportIdUpdated'];

    public function mount(int $airportId)
    {
        $this->airportId = $airportId;
    }

    public function airportIdUpdated(int $airportId)
    {
        $this->airportId = $airportId;
    }

    protected function getCards(): array
    {
        return [
            Card::make('Total Inbound', $this->getCardValue('getTotalInbound')),
            Card::make('Landed Last 10 Minutes', $this->getCardValue('getLandedLast10Minutes')),
            Card::make('Departing Within 400nm', $this->getCardValue('getDepartingNearby')),
            Card::make('Ground Within 400nm', $this->getCardValue('getGroundNearby')),
        ];
    }

    private function getCardValue(string $name): int|string
    {
        $statistics = app()->make(AirportStatistics::class);
        return $this->airportId ? $statistics->$name($this->airportId) : '--';
    }
}
