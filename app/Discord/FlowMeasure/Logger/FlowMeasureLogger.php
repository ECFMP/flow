<?php

namespace App\Discord\FlowMeasure\Logger;

use App\Discord\Message\Logger\LoggerInterface;
use App\Enums\DiscordNotificationType;
use App\Models\DivisionDiscordNotification;
use App\Models\FlowMeasure;

class FlowMeasureLogger implements LoggerInterface
{
    private readonly FlowMeasure $flowMeasure;
    private readonly DiscordNotificationType $type;

    public function __construct(FlowMeasure $flowMeasure, DiscordNotificationType $type)
    {
        $this->flowMeasure = $flowMeasure;
        $this->type = $type;
    }

    public function log(DivisionDiscordNotification $notification): void
    {
        activity()
            ->inLog('Discord')
            ->performedOn($notification)
            ->event(sprintf('%s - %s', $this->flowMeasure->identifier, $this->type->name()))
            ->causedByAnonymous()
            ->withProperties(
                [
                    'to' => $notification?->divisionDiscordWebhook?->description ?? 'ECFMP',
                    'type' => $this->type->name(),
                    'content' => $notification->content,
                    'embeds' => json_encode($notification->embeds),
                ]
            )
            ->log('Sending discord notification');
    }
}
