<?php

namespace App\Discord\FlowMeasure\Content;

class Reason extends AbstractFlowMeasureContent
{
    public function toString(): string
    {
        return sprintf('DUE: %s', $this->flowMeasure->reason ?? 'No reason given');
    }
}
