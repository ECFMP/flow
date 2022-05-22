<?php

namespace App\Discord\FlowMeasure\Field\Filters;

class ViaWaypoint extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Via Waypoint';
    }

    public function value(): string
    {
        return (string) $this->filter['value'];
    }
}
