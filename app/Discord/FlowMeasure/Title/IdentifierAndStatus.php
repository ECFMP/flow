<?php


namespace App\Discord\FlowMeasure\Title;


use Carbon\Carbon;

class IdentifierAndStatus extends AbstractFlowMeasureTitle
{
    public function title(): string
    {
        return sprintf(
            '%s - %s',
            $this->flowMeasure->identifier,
            $this->status()
        );
    }

    private function status(): string
    {
        if ($this->flowMeasure->deleted_at !== null) {
            return 'Withdrawn';
        }

        if ($this->flowMeasure->start_time > Carbon::now()) {
            return 'Notified';
        }

        return $this->flowMeasure->end_time > Carbon::now()
            ? 'Active'
            : 'Expired';
    }
}
