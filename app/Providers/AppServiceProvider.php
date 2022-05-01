<?php

namespace App\Providers;

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
        FlowMeasureResource::withoutWrapping();
    }
}
