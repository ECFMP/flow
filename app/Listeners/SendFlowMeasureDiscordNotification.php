<?php

namespace App\Listeners;

use App\Discord\DiscordInterface;
use App\Discord\DiscordMessage;
use App\Events\FlowMeasureCreatedEvent;

class SendFlowMeasureDiscordNotification
{
    private readonly DiscordInterface $discord;

    public function __construct(DiscordInterface $discord)
    {
        $this->discord = $discord;
    }

    public function handle(FlowMeasureCreatedEvent $event): void
    {
        $this->discord->sendMessage(new DiscordMessage());
    }
}
