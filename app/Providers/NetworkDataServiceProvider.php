<?php

namespace App\Providers;

use App\Vatsim\NetworkDataDownloader;
use App\Vatsim\Processor\Pilot\DistanceToDestination;
use App\Vatsim\Processor\Pilot\EstimatedArrivalTime;
use App\Vatsim\Processor\Pilot\PilotProcessor;
use App\Vatsim\Processor\Pilot\PilotStatus;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class NetworkDataServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(
            NetworkDataDownloader::class,
            fn(Application $application, array $overrideParams) => new NetworkDataDownloader($this->resolveProcessors($overrideParams))
        );

        $this->app->bind(
            PilotProcessor::class,
            fn() => new PilotProcessor(
                [
                        $this->app->make(PilotStatus::class),
                        $this->app->make(EstimatedArrivalTime::class),
                        $this->app->make(DistanceToDestination::class),
                ]
            )
        );
    }

    private function resolveProcessors(array $overrideParams): array
    {
        return isset($overrideParams['processors']) ? $overrideParams['processors'] : $this->defaultProcessors();
    }

    private function defaultProcessors(): array
    {
        return [$this->app->make(PilotProcessor::class)];
    }
}
