<?php

namespace App\Discord\FlowMeasure\Field;

use App\Models\FlightInformationRegion;
use Illuminate\Support\Arr;

class ApplicableTo extends AbstractFlowMeasureField
{
    public function name(): string
    {
        return 'Applicable To FIR(s)';
    }

    public function value(): string
    {
        return $this->flowMeasure->notifiedFlightInformationRegions->isEmpty()
            ? '--'
            : Arr::join(
                $this->flowMeasure->notifiedFlightInformationRegions->map(
                    fn (FlightInformationRegion $flightInformationRegion) => $flightInformationRegion->identifier
                )
                    ->toArray(),
                ', '
            );
    }
}
