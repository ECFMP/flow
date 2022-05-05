<?php

namespace App\Discord\FlowMeasure\Content;

use App\Enums\FilterType;
use InvalidArgumentException;

class Departing extends AbstractFlowMeasureContent
{
    use FormatsAirports;

    public function toString(): string
    {
        $filters = $this->flowMeasure->filtersByType(FilterType::DEPARTURE_AIRPORTS);
        if (count($filters) !== 1) {
            throw new InvalidArgumentException('Must have at least one departure airport');
        }
        $airports = $filters[0]['value'];

        return sprintf(
            'ADEP: %s',
            $this->formatAirports($airports)
        );
    }
}
