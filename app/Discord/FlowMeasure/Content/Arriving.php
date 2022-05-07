<?php

namespace App\Discord\FlowMeasure\Content;

use App\Enums\FilterType;
use App\Models\FlowMeasure;
use InvalidArgumentException;

class Arriving extends AbstractFlowMeasureContent
{
    use FormatsAirports;

    public function toString(): string
    {
        $filters = $this->flowMeasure->filtersByType(FilterType::ARRIVAL_AIRPORTS);
        if (count($filters) !== 1) {
            throw new InvalidArgumentException('Must have at least one arrival airport');
        }
        $airports = $filters[0]['value'];

        return sprintf(
            'DEST: %s',
            $this->formatAirports($airports)
        );
    }
}
