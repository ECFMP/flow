<?php

namespace App\Discord\FlowMeasure\Field;

class StartTime extends AbstractFlowMeasureField
{
    use UsesTimes;

    public function name(): string
    {
        return 'Start Time';
    }

    public function value(): string
    {
        return $this->dateTime($this->flowMeasure->start_time);
    }
}
