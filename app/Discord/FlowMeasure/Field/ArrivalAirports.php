<?php

namespace App\Discord\FlowMeasure\Field;

use App\Enums\FilterType;
use InvalidArgumentException;

class ArrivalAirports extends AbstractFlowMeasureField
{
    use FormatsAirports;

    public function name(): string
    {
        return 'Arrival Airports';
    }

    public function value(): string
    {
        $filters = $this->flowMeasure->filtersByType(FilterType::ARRIVAL_AIRPORTS);
        if (count($filters) !== 1) {
            throw new InvalidArgumentException('Must have at least one arrival airport');
        }

        return $this->formatAirports($filters[0]['value']);
    }
}
