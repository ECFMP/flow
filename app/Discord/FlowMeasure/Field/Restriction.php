<?php

namespace App\Discord\FlowMeasure\Field;

use App\Enums\FlowMeasureType;
use Arr;
use InvalidArgumentException;

class Restriction extends AbstractFlowMeasureField
{
    public function name(): string
    {
        return $this->flowMeasure->type->getFormattedName();
    }

    public function value(): string
    {
        return match ($this->flowMeasure->type) {
            FlowMeasureType::MANDATORY_ROUTE => $this->mandatoryRouteValue(),
            FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL => $this->timeValue(
            ),
            FlowMeasureType::PER_HOUR => $this->perHourValue(),
            FlowMeasureType::MILES_IN_TRAIL => $this->nauticalMilesValue(),
            FlowMeasureType::MAX_IAS, FlowMeasureType::IAS_REDUCTION => $this->indicatedAirspeedValue(),
            FlowMeasureType::MAX_MACH, FlowMeasureType::MACH_REDUCTION => $this->machNumberValue(),
            default => throw new InvalidArgumentException('Invalid measure type')
        };
    }

    private function mandatoryRouteValue(): string
    {
        return Arr::join($this->flowMeasure->mandatory_route, ', ');
    }

    private function timeValue(): string
    {
        $mins = (int)($this->flowMeasure->value / 60);
        $seconds = $this->flowMeasure->value % 60;

        if ($mins === 0) {
            return sprintf('%d SECS', $seconds);
        }

        if ($seconds === 0) {
            return sprintf('%d MINS', $mins);
        }

        return sprintf('%d MINS %d SECS', $mins, $seconds);
    }

    private function indicatedAirspeedValue(): string
    {
        return sprintf('%d kts', $this->flowMeasure->value);
    }

    private function machNumberValue(): string
    {
        return number_format($this->flowMeasure->value / 100, 2);
    }

    private function nauticalMilesValue(): string
    {
        return sprintf('%s NM', $this->flowMeasure->value);
    }

    private function perHourValue(): string
    {
        return (string)$this->flowMeasure->value;
    }
}
