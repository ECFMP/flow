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

    public function getShortName(): string
    {
        return match ($this) {
            self::DEPARTURE_AIRPORTS => 'DEPA',
            self::ARRIVAL_AIRPORTS => 'DEST',
            self::WAYPOINT => 'VIA WPT',
            self::LEVEL_ABOVE => 'LVL ABV',
            self::LEVEL_BELOW => 'LVL BLW',
            self::LEVEL => 'LVL',
            self::MEMBER_EVENT => 'EVENT',
            self::MEMBER_NOT_EVENT => 'NON EVENT',
        };
    }
}
