<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use App\Http\Resources\EventResource;
use Illuminate\Support\ServiceProvider;
use App\Http\Resources\FlowMeasureResource;
use App\Http\Resources\AirportGroupResource;
use App\Http\Resources\FlightInformationRegionResource;

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

        Filament::pushMeta([
            new HtmlString('<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">'),
            new HtmlString('<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">'),
            new HtmlString('<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">'),
            new HtmlString('<link rel="manifest" href="/site.webmanifest">'),
            new HtmlString('<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">'),
            new HtmlString('<meta name="msapplication-TileColor" content="#603cba">'),
            new HtmlString('<meta name="theme-color" content="#ffffff">'),
        ]);
    }
}
