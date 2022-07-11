<?php

namespace App\Discord\FlowMeasure\Title;

use App\Discord\Message\Embed\TitleInterface;
use App\Models\FlowMeasure;

abstract class AbstractFlowMeasureTitle implements TitleInterface
{
    protected readonly FlowMeasure $flowMeasure;

    public function __construct(FlowMeasure $flowMeasure)
    {
        $this->flowMeasure = $flowMeasure;
    }

    protected function formatIdentifierAndStatus(string $status): string
    {
        return sprintf('%s - %s', $this->flowMeasure->identifier, $status);
    }

    protected function formatIdentifierStatusAndReissued(string $status, bool $reissued): string
    {
        return sprintf(
            '%s%s',
            $this->formatIdentifierAndStatus($status),
            $reissued ? ' (Reissued)' : ''
        );
    }
}
