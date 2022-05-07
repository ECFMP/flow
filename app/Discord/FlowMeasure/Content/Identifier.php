<?php

namespace App\Discord\FlowMeasure\Content;

use App\Models\FlowMeasure;

class Identifier extends AbstractFlowMeasureContent
{
    public function toString(): string
    {
        return sprintf(
            '%s%s',
            $this->flowMeasure->identifier,
            $this->flowMeasure->event ? ' - ' . $this->flowMeasure->event->name : ''
        );
    }
}
