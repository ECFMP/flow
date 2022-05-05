<?php

namespace App\Discord\FlowMeasure\Content;

use Arr;

class Measure extends AbstractFlowMeasureContent
{
    public function toString(): string
    {
        return sprintf(
            '%s: %s',
            $this->flowMeasure->type->getFormattedName(),
            $this->value()
        );
    }

    private function value(): string
    {
        if ($this->flowMeasure->isMandatoryRoute()) {
            return Arr::join($this->flowMeasure->mandatory_route, ',');
        }

        $mins = (int)$this->flowMeasure->value / 60;
        $seconds = (int)$this->flowMeasure->value % 60;

        if ($mins === 0) {
            return sprintf('%d SECS', $seconds);
        }

        if ($seconds === 0) {
            return sprintf('%d MINS', $mins);
        }

        return sprintf('%d MINS %d SECS', $mins, $seconds);
    }
}
