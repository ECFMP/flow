<?php

namespace App\Events;

use App\Models\FlowMeasure;

class FlowMeasureCreatedEvent
{
    private readonly FlowMeasure $flowMeasure;

    public function __construct(FlowMeasure $flowMeasure)
    {
        $this->flowMeasure = $flowMeasure;
    }

    public function flowMeasure(): FlowMeasure
    {
        return $this->flowMeasure;
    }
}
