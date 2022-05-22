<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\FlowMeasure\Field\AbstractFlowMeasureField;

class Level extends AbstractFlowMeasureFilterField
{
    public function name(): string
    {
        return 'At Levels';
    }

    public function value(): string
    {
        return (string) $this->filter['value'];
    }
}
