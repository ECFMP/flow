<?php

namespace App\Discord\FlowMeasure\Description;

class EventName extends AbstractFlowMeasureDescription
{
    public function description(): string
    {
        return $this->flowMeasure?->event->name ?? '';
    }
}
