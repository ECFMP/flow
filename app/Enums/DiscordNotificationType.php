<?php

namespace App\Enums;

enum DiscordNotificationType: string
{
    case FLOW_MEASURE_NOTIFIED = 'flow_measure_notified';
    case FLOW_MEASURE_ACTIVATED = 'flow_measure_activated';
    case FLOW_MEASURE_WITHDRAWN = 'flow_measure_withdrawn';
    case FLOW_MEASURE_EXPIRED = 'flow_measure_expired';

    public function name()
    {
        return match ($this) {
            DiscordNotificationType::FLOW_MEASURE_NOTIFIED => 'Notified',
            DiscordNotificationType::FLOW_MEASURE_ACTIVATED => 'Activated',
            DiscordNotificationType::FLOW_MEASURE_WITHDRAWN => 'Withdrawn',
            DiscordNotificationType::FLOW_MEASURE_EXPIRED => 'Expired',
        };
    }
}
