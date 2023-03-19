<?php

namespace App\Jobs;

use App\Discord\FlowMeasure\Sender\Sender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendDiscordNotifications implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private readonly Sender $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function handle(): void
    {
        if (!config('discord.enabled')) {
            Log::info('Skipping discord notifications');
            return;
        }

        Log::info('Sending discord notifications');
        $this->sender->sendDiscordMessages();
        Log::info('Discord notification sending complete');
    }
}
