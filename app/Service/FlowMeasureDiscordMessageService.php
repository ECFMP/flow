<?php

namespace App\Service;

use App\Discord\DiscordInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedMessage;
use App\Discord\Message\MessageInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\NoReturn;

class FlowMeasureDiscordMessageService
{
    private readonly DiscordInterface $discord;

    public function __construct(DiscordInterface $discord)
    {
        $this->discord = $discord;
    }

    public function sendMeasureActivatedDiscordNotifications(): void
    {
        FlowMeasure::whereDoesntHave('discordNotifications', function (Builder $notification) {
            $notification->type(DiscordNotificationType::FLOW_MEASURE_ACTIVATED);
        })
            ->active()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
                    new FlowMeasureActivatedMessage(FlowMeasureContentBuilder::activated($flowMeasure))
                );
            });
    }

    #[NoReturn] private function sendDiscordNotification(
        FlowMeasure $flowMeasure,
        DiscordNotificationType $type,
        MessageInterface $message
    ): void {
        $flowMeasure->discordNotifications()->create(
            [
                'type' => $type,
                'content' => $message->content(),
            ]
        );
        $this->discord->sendMessage($message);
    }
}
