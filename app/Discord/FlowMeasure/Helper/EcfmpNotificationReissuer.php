<?php

namespace App\Discord\FlowMeasure\Helper;

use App\Enums\DiscordNotificationType;
use App\Repository\FlowMeasureNotification\FlowMeasureForNotification;

class EcfmpNotificationReissuer implements NotificationReissuerInterface
{
    private readonly FlowMeasureForNotification $flowMeasureForNotification;
    private readonly DiscordNotificationType $type;

    public function __construct(FlowMeasureForNotification $flowMeasureForNotification, DiscordNotificationType $type)
    {
        $this->flowMeasureForNotification = $flowMeasureForNotification;
        $this->type = $type;
    }

    public function isReissuedNotification(): bool
    {
        return ($this->type === DiscordNotificationType::FLOW_MEASURE_ACTIVATED || $this->type === DiscordNotificationType::FLOW_MEASURE_NOTIFIED)
            && $this->flowMeasureForNotification->isReissuedNotification;
    }
}
