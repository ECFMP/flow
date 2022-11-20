<?php

namespace App\Filament\Widgets;

use App\Helpers\AirportStatistics;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AirportOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected ?int $airportId = null;

    public function mount(int $airportId)
    {
        $this->airportId = $airportId;
    }

    protected function getCards(): array
    {
        return [
            Card::make('Total Inbound', $this->getCardValue('getTotalInbound')),
            Card::make('Inbound Next 15 Minutes', $this->getCardValue('getInbound15Minutes')),
            Card::make('Inbound Next 30 Minutes', $this->getCardValue('getInbound30Minutes')),
            Card::make('Inbound Next 60 Minutes', $this->getCardValue('getInbound60Minutes')),
        ];
    }

    private function getCardValue(string $name): int|string
    {
        $statistics = app()->make(AirportStatistics::class);
        return $this->airportId ? $statistics->$name($this->airportId) : '--';
    }
}
