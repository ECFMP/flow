<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\FlowMeasure\Field\AbstractFlowMeasureField;

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
