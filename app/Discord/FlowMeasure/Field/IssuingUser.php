<?php

namespace App\Discord\FlowMeasure\Field;

class IssuingUser extends AbstractFlowMeasureField
{
    public function name(): string
    {
        return 'Issued By';
    }

    public function value(): string
    {
        return $this->flowMeasure->user->name;
    }
}
