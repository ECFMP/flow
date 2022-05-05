<?php

namespace App\Discord\FlowMeasure\Content;

class ValidPeriod extends AbstractFlowMeasureContent
{
    private const TIME_FORMAT = 'Hi';
    private const DATE_TIME_FORMAT = 'd/m ' . self::TIME_FORMAT;

    public function toString(): string
    {
        return sprintf('VALID: %s', $this->getTimeString());
    }

    private function getTimeString(): string
    {
        if ($this->flowMeasure->start_time->isSameDay($this->flowMeasure->end_time)) {
            return sprintf(
                '%s-%sZ',
                $this->flowMeasure->start_time->format(self::DATE_TIME_FORMAT),
                $this->flowMeasure->end_time->format(self::TIME_FORMAT)
            );
        }

        return sprintf(
            '%s - %sZ',
            $this->flowMeasure->start_time->format(self::DATE_TIME_FORMAT),
            $this->flowMeasure->end_time->format(self::DATE_TIME_FORMAT)
        );
    }
}
