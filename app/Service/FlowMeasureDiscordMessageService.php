<?php

namespace App\Service;

use App\Discord\DiscordInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedMessage;
use App\Models\FlowMeasure;
use JetBrains\PhpStorm\NoReturn;

class FlowMeasureDiscordMessageService
{
    private readonly DiscordInterface $discord;

    public function __construct(DiscordInterface $discord)
    {
        $this->discord = $discord;
    }

    public function sendDiscordNotifications(): void
    {
        FlowMeasure::whereDoesntHave('discordNotification')
            ->active()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification($flowMeasure);
            });
    }

    public function sendDiscordNotificationIfRequired(FlowMeasure $flowMeasure): void
    {
        if (!$flowMeasure->isActive()) {
            return;
        }

        $this->sendDiscordNotification($flowMeasure);
    }

    #[NoReturn] private function sendDiscordNotification(FlowMeasure $flowMeasure): void
    {
        $message = new FlowMeasureActivatedMessage(FlowMeasureContentBuilder::build($flowMeasure));
        $flowMeasure->discordNotification()->create(['content' => $message->content()]);
        $this->discord->sendMessage($message);
    }
}
