<?php

namespace App\Discord\FlowMeasure\Content;

use App\Enums\FilterType;
use App\Models\Event;
use Illuminate\Support\Arr;

class OtherFilters extends AbstractFlowMeasureContent
{
    public function toString(): string
    {
        $returnValue = '';

        foreach ($this->flowMeasure->extraFilters() as $filter) {
            $returnValue .= sprintf(
                '%s: %s%s',
                FilterType::from($filter['type'])->getShortName(),
                match (FilterType::from($filter['type'])) {
                    FilterType::WAYPOINT => $this->formatList($filter['value']),
                    FilterType::LEVEL_ABOVE, FilterType::LEVEL_BELOW, FilterType::LEVEL => (string)$filter['value'],
                    FilterType::MEMBER_EVENT, FilterType::MEMBER_NOT_EVENT => $this->eventName($filter['value']),
                    default => 'Unknown filter'
                },
                "\n"
            );
        }

        return rtrim($returnValue);
    }

    private function formatList(array $waypoints): string
    {
        return Arr::join($waypoints, ' ');
    }

    private function eventName(array $event): string
    {
        return Event::findOrFail($event['value']['event_id'])->name;
    }
}
