<?php

namespace App\Discord\FlowMeasure\Field;

class Reason extends AbstractFlowMeasureField
{
    public function name(): string
    {
        return 'Reason';
    }

    public function value(): string
    {
        return $this->flowMeasure->reason ?? 'No reason given';
    }
}
