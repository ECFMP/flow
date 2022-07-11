<?php

namespace App\Discord\FlowMeasure\Field\Filters;

class Level extends AbstractFlowMeasureFilterField
{
    public function name(): string
    {
        return 'At Levels';
    }

    public function value(): string
    {
        return $this->joinedValues("\n");
    }
}
