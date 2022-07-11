<?php

namespace App\Discord\FlowMeasure\Title;

class IdentifierAndExpiredStatus extends AbstractFlowMeasureTitle
{
    public function title(): string
    {
        return $this->formatIdentifierAndStatus('Expired');
    }
}
