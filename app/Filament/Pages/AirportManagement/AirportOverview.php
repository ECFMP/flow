<?php

namespace App\Filament\Pages\AirportManagement;

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
            Card::make('Inbound Next 30 Minutes', $this->getCardValue('getInbound30Minutes')),
            Card::make('Inbound Next 30-60 Minutes', $this->getCardValue('getInbound30To60Minutes')),
            Card::make('Inbound Next 60-120 Minutes', $this->getCardValue('getInbound60To120Minutes')),
            Card::make('Landed Last 10 Minutes', $this->getCardValue('getLandedLast10Minutes')),
            Card::make('Awaiting Departure', $this->getCardValue('getAwaitingDeparture')),
            Card::make('Departing Within 400nm', $this->getCardValue('getDepartingNearby')),
        ];
    }

    private function getCardValue(string $name): int|string
    {
        $statistics = app()->make(AirportStatistics::class);
        return $this->airportId ? $statistics->$name($this->airportId) : '--';
    }
}
