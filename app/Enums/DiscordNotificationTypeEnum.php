<?php

namespace App\Enums;

enum DiscordNotificationTypeEnum: string
{
    case FLOW_MEASURE_NOTIFIED = 'flow_measure_notified';
    case FLOW_MEASURE_ACTIVATED = 'flow_measure_activated';
    case FLOW_MEASURE_WITHDRAWN = 'flow_measure_withdrawn';
    case FLOW_MEASURE_EXPIRED = 'flow_measure_expired';
}
