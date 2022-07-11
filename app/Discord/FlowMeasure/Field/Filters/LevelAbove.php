<?php

namespace App\Discord\FlowMeasure\Field\Filters;

class LevelAbove extends AbstractFlowMeasureFilterField
{
    public function name(): string
    {
        return 'Level at or Above';
    }

    public function value(): string
    {
        return $this->filter['value'];
    }
}
