<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\Message\Embed\FieldProviderInterface;
use App\Enums\FilterType;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class AdditionalFilterParser
{
    public static function parseAdditionalFilters(FlowMeasure $flowMeasure): Collection
    {
        return collect(
            array_map(
                fn (array $filter) => self::getFilter($filter),
                $flowMeasure->extraFilters()
            )
        );
    }

    private static function getFilter(array $filter): FieldProviderInterface
    {
        return match (FilterType::from($filter['type'])) {
            FilterType::WAYPOINT => new ViaWaypoint($filter),
            FilterType::LEVEL => new Level($filter),
            FilterType::LEVEL_ABOVE => new LevelAbove($filter),
            FilterType::LEVEL_BELOW => new LevelBelow($filter),
            FilterType::MEMBER_EVENT => new MemberEvent($filter),
            FilterType::MEMBER_NOT_EVENT => new MemberNotEvent($filter),
            FilterType::RANGE_TO_DESTINATION => new RangeToDestination($filter),
            default => throw new InvalidArgumentException(),
        };
    }
}
