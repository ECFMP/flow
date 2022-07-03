<?php

namespace App\Providers;

use App\Discord\DiscordInterface;
use App\Discord\DiscordMessageSender;
use App\Discord\Webhook\EcfmpWebhook;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DiscordInterface::class, function () {
            return new DiscordMessageSender();
        });
        $this->app->singleton(EcfmpWebhook::class);
    }
}
