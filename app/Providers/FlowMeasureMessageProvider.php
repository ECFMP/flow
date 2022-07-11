<?php

namespace App\Providers;

use App\Discord\FlowMeasure\Provider\ActivatedMessageProvider;
use App\Repository\FlowMeasureNotification\ActiveRepository;
use Illuminate\Support\ServiceProvider;

class FlowMeasureMessageProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();
        $this->app->singleton(ActivatedMessageProvider::class, function () {
            return new ActivatedMessageProvider($this->app->make(ActiveRepository::class));
        });
    }
}
