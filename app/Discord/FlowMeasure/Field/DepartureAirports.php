<?php

namespace App\Discord\FlowMeasure\Field;

use App\Enums\FilterType;
use InvalidArgumentException;

class DepartureAirports extends AbstractFlowMeasureField
{
    use FormatsAirports;

    public function name(): string
    {
        return 'Departure Airports';
    }

    public function value(): string
    {
        $filters = $this->flowMeasure->filtersByType(FilterType::DEPARTURE_AIRPORTS);
        if (count($filters) !== 1) {
            throw new InvalidArgumentException('Must have at least one departure airport');
        }

        return $this->formatAirports($filters[0]['value']);
    }
}
