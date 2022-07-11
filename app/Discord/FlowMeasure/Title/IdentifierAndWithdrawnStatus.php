<?php

namespace App\Discord\FlowMeasure\Title;

class IdentifierAndWithdrawnStatus extends AbstractFlowMeasureTitle
{
    public function title(): string
    {
        return $this->formatIdentifierAndStatus('Withdrawn');
    }
}
