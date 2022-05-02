<?php

namespace App\Providers;

use App\Http\Resources\AirportGroupResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        AirportGroupResource::withoutWrapping();
        EventResource::withoutWrapping();
        FlowMeasureResource::withoutWrapping();
        FlightInformationRegionResource::withoutWrapping();
    }
}
