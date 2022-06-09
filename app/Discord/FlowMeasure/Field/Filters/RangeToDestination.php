<?php

namespace App\Discord\FlowMeasure\Field\Filters;

class RangeToDestination extends AbstractFlowMeasureFilterField
{
    public function name(): string
    {
        return 'Range To Destination (NM)';
    }

    public function value(): string
    {
        return $this->joinedValues("\n");
    }
}
