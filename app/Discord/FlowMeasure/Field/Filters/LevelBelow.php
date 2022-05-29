<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\FlowMeasure\Field\AbstractFlowMeasureField;

class LevelBelow extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Level at or Below';
    }

    public function value(): string
    {
        return $this->filter['value'];
    }
}
