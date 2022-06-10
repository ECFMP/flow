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
            return $this->withReissued('Notified');
        }

        return $this->flowMeasure->end_time > Carbon::now()
            ? $this->withReissued('Active')
            : 'Expired';
    }

    private function withReissued(string $status): string
    {
        $lastNotification = $this->flowMeasure->discordNotifications()
            ->latest()
            ->first();

        return !$lastNotification || $lastNotification->pivot->notified_as === $this->flowMeasure->identifier
            ? $status
            : $status . ' (Reissued)';
    }
}
