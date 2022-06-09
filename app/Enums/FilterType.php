<?php

namespace App\Enums;

enum FilterType: string
{
    case DEPARTURE_AIRPORTS = 'ADEP';
    case ARRIVAL_AIRPORTS = 'ADES';
    case WAYPOINT = 'waypoint';
    case LEVEL_ABOVE = 'level_above';
    case LEVEL_BELOW = 'level_below';
    case LEVEL = 'level';
    case MEMBER_EVENT = 'member_event';
    case MEMBER_NOT_EVENT = 'member_not_event';
    case RANGE_TO_DESTINATION = 'range_to_destination';
}
