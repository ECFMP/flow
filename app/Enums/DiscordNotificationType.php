<?php

namespace App\Enums;

enum DiscordNotificationType: string
{
    case FLOW_MEASURE_APPROACHING = 'flow_measure_approaching';
    case FLOW_MEASURE_ACTIVATED = 'flow_measure_activated';
    case FLOW_MEASURE_WITHDRAWN = 'flow_measure_withdrawn';
    case FLOW_MEASURE_EXPIRED = 'flow_measure_expired';
}
