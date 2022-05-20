<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\FlowMeasure\Field\AbstractFlowMeasureField;

class LevelAbove extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Level at or Above';
    }

    public function value(): string
    {
        return (string) $this->filter['value'];
    }
}
