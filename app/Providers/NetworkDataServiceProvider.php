<?php

namespace App\Providers;

use App\Vatsim\NetworkDataDownloader;
use App\Vatsim\Processor\Pilot\PilotProcessor;
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
