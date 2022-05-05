<?php

namespace App\Providers;

use App\Discord\DiscordInterface;
use App\Discord\DiscordMessageSender;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DiscordInterface::class, function () {
            return new DiscordMessageSender();
        });
    }
}
