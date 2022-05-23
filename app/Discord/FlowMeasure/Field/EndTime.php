<?php

namespace App\Discord\FlowMeasure\Field;

class EndTime extends AbstractFlowMeasureField
{
    use UsesTimes;

    public function name(): string
    {
        return 'End Time';
    }

    public function value(): string
    {
        return $this->flowMeasure->start_time->isSameDay($this->flowMeasure->end_time)
            ? $this->shortTime($this->flowMeasure->end_time)
            : $this->dateTime($this->flowMeasure->end_time);
    }
}
