<?php

namespace App\Filament\Pages\AirportTraffic;

use App\Helpers\AirportStatistics;
use Filament\Widgets\BarChartWidget;

class InboundGroupsGraph extends BarChartWidget
{
    protected static ?string $pollingInterval = '60s';

    public $airportId;

    protected $listeners = ['airportIdUpdated'];

    protected static ?array $options = [
        'barPercentage' => 0.6,
        'scales' => [
            'y' => [
                'beginAtZero' => true,
                'ticks' => [
                    'precision' => 0,
                ],
            ],
        ],
    ];

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
                    'backgroundColor' => $graphData->values()->map(
                        fn (int $count): string => $this->getBarColour($count)
                    )->toArray(),
                    'borderColor' => $graphData->values()->map(
                        fn (int $count): string => $this->getBorderColour($count)
                    )->toArray(),
                    'borderWidth' => 1.5,
                ]
            ],
            'labels' => $graphData->keys()
                ->map(fn (int $key): string => sprintf('%d - %d minutes', $key - 30, $key))
                ->toArray(),
        ];
    }

    private function getBarColour(int $inboundCount): string
    {
        if ($inboundCount < 5) {
            return 'rgba(0, 192, 0, 0.4)';
        }

        if ($inboundCount <= 10) {
            return 'rgba(255, 205, 86, 0.4)';
        }

        if ($inboundCount <= 15) {
            return 'rgba(255, 159, 64, 0.4)';
        }

        return 'rgba(255, 99, 132, 0.4)';
    }

    private function getBorderColour(int $inboundCount): string
    {
        if ($inboundCount < 5) {
            return 'rgb(0, 192, 0)';
        }

        if ($inboundCount <= 10) {
            return 'rgb(255, 205, 86)';
        }

        if ($inboundCount <= 15) {
            return 'rgb(255, 159, 64)';
        }

        return 'rgb(255, 99, 132)';
    }
}
