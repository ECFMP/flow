<?php

namespace App\Jobs;

use App\Discord\FlowMeasure\Generator\EcfmpFlowMeasureMessageGenerator;
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

    // Unique for 2 minutes
    public $uniqueFor = 120;

    private readonly DivisionWebhookSender $sender;
    private readonly EcfmpFlowMeasureMessageGenerator $generator;

    public function __construct(DivisionWebhookSender $sender, EcfmpFlowMeasureMessageGenerator $generator)
    {
        $this->sender = $sender;
        $this->generator = $generator;
    }

    public function handle(): void
    {
        if (!config('discord.enabled')) {
            Log::info('Skipping discord notifications');
            return;
        }

        Log::info('Sending discord notifications');
        $this->sender->sendDiscordMessages();
        $this->generator->generateAndSend();
        Log::info('Discord notification sending complete');
    }
}
