<?php

namespace App\Enums;

enum FlowMeasureType: string
{
    case MINIMUM_DEPARTURE_INTERVAL = 'minimum_departure_interval';
    case AVERAGE_DEPARTURE_INTERVAL = 'average_departure_interval';
    case PER_HOUR = 'per_hour';
    case MILES_IN_TRAIL = 'miles_in_trail';
    case MAX_IAS = 'max_ias';
    case MAX_MACH = 'max_mach';
    case IAS_REDUCTION = 'ias_reduction';
    case MACH_REDUCTION = 'mach_reduction';
    case PROHIBIT = 'prohibit';
    case MANDATORY_ROUTE = 'mandatory_route';

    public function getFormattedName(): string
    {
        return match ($this) {
            self::MINIMUM_DEPARTURE_INTERVAL => 'Minimum Departure Interval [MDI]',
            self::AVERAGE_DEPARTURE_INTERVAL => 'Average Departure Interval [ADI]',
            self::PER_HOUR => 'Per hour',
            self::MILES_IN_TRAIL => 'Miles In Trail [MIT]',
            self::MAX_IAS => 'Max IAS',
            self::MAX_MACH => 'Max Mach',
            self::IAS_REDUCTION => 'IAS reduction',
            self::MACH_REDUCTION => 'Mach reduction',
            self::PROHIBIT => 'Prohibit',
            self::MANDATORY_ROUTE => 'Mandatory route',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::MINIMUM_DEPARTURE_INTERVAL => 'Minimum departure interval in seconds',
            self::AVERAGE_DEPARTURE_INTERVAL => 'Average departure interval applied over 3 aircraft in seconds',
            self::PER_HOUR => 'Number of flights per hour permitted - must be > 0',
            self::MILES_IN_TRAIL => 'Distance in NM of aircraft in trail',
            self::MAX_IAS => 'Maximum speed in IAS',
            self::MAX_MACH => 'Maximum speed in mach, 82 = 0.82 Mach, 102 = 1.02 Mach',
            self::IAS_REDUCTION => 'Reduce indicated airspeed by certain value in knots',
            self::MACH_REDUCTION => 'Reduced mach by certain value 5 = 0.05 Mach',
            self::PROHIBIT => 'Prohibit a flight according to filters',
            self::MANDATORY_ROUTE => 'Mandate routing via a single waypoint/airway only',
            default => '',
        };
    }
}
