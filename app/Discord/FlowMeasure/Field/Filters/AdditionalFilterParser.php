<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\Message\Embed\FieldProviderInterface;
use App\Enums\FilterType;
use App\Models\FlowMeasure;
use InvalidArgumentException;

class AdditionalFilterParser
{
    public static function parseAdditionalFilters(FlowMeasure $flowMeasure): array
    {
        return array_map(
            fn (array $filter) => $this->getFilter($filter),
            $flowMeasure->extraFilters()
        );
    }

    private function getFilter(array $filter): FieldProviderInterface
    {
        return match (FilterType::from($filter['type'])) {
            FilterType::WAYPOINT => new ViaWaypoint($filter),
            FilterType::LEVEL => new Level($filter),
            FilterType::LEVEL_ABOVE => new LevelAbove($filter),
            FilterType::LEVEL_BELOW => new LevelBelow($filter),
            FilterType::MEMBER_EVENT => new MemberEvent($filter),
            FilterType::MEMBER_NOT_EVENT => new MemberNotEvent($filter),
            default => throw new InvalidArgumentException(),
        };
    }
}
