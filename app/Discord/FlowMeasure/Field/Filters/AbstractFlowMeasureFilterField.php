<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\Message\Embed\FieldProviderInterface;
use App\Models\Event;
use Illuminate\Support\Arr;

abstract class AbstractFlowMeasureFilterField implements FieldProviderInterface
{
    protected readonly array $filter;

    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }

    protected function joinedValues(string $glue = ', '): string
    {
        return Arr::join($this->filter['value'], $glue);
    }

    protected function eventName(): string
    {
        return Arr::join(
            array_map(
                fn(int $eventId) => Event::withTrashed()->findOrFail($eventId)->name,
                $this->filter['value']
            ),
            ', '
        );
    }
}
