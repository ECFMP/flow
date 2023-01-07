<?php

namespace App\Filament\Pages\AirportTraffic;

use App\Helpers\AirportStatistics;
use Filament\Widgets\LineChartWidget;

class InboundGroupsGraph extends LineChartWidget
{
    protected static ?string $pollingInterval = '60s';

    public $airportId;

    protected $listeners = ['airportIdUpdated'];

    public function airportIdUpdated(int $airportId)
    {
        $this->airportId = $airportId;
        $this->updateChartData();
    }

    protected function getHeading(): ?string
    {
        return 'Inbounds';
    }

    protected function getData(): array
    {
        if (!$this->airportId) {
            return [];
        }

        $statistics = app()->make(AirportStatistics::class);
        $graphData = $statistics->getInboundGraphData($this->airportId);

        if (empty([$graphData])) {
            return [];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inbounds',
                    'data' => $graphData->values()->toArray(),
                ]
            ],
            'labels' => $graphData->keys()
                ->map(fn (int $key): string => sprintf('%d - %d minutes', $key - 30, $key))
                ->toArray(),
        ];
    }
}
