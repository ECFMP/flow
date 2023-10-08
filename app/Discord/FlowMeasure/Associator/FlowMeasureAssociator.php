<?php

namespace App\Discord\FlowMeasure\Associator;

use App\Discord\Message\Associator\AssociatorInterface;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;

class FlowMeasureAssociator implements AssociatorInterface
{
    private readonly FlowMeasure $flowMeasure;
    private readonly DiscordNotificationTypeEnum $type;

    public function __construct(FlowMeasure $flowMeasure, DiscordNotificationTypeEnum $type)
    {
        $this->flowMeasure = $flowMeasure;
        $this->type = $type;
    }


    public function associate(DivisionDiscordNotification $notification): void
    {
        $this->flowMeasure->discordNotifications()->attach(
            [
                $notification->id => [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum($this->type),
                    'notified_as' => $this->flowMeasure->identifier,
                ],
            ]
        );
    }
}
