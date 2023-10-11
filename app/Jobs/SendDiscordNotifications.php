<?php

namespace App\Jobs;

use App\Discord\Message\Sender\DivisionWebhookSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendDiscordNotifications implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private readonly DivisionWebhookSender $sender;

    public function __construct(DivisionWebhookSender $sender)
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
