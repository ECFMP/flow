<?php

namespace App\Providers;

use App\Vatsim\NetworkDataDownloader;
use App\Vatsim\Processor\PilotProcessor;
use Illuminate\Support\ServiceProvider;

class NetworkDataServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(
            NetworkDataDownloader::class,
            fn() => new NetworkDataDownloader([
                    $this->app->make(PilotProcessor::class),
            ])
        );
    }
}
