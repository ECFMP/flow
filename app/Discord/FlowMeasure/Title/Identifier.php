<?php


namespace App\Discord\FlowMeasure\Title;


class Identifier extends AbstractFlowMeasureTitle
{
    public function title(): string
    {
        return $this->flowMeasure->identifier;
    }
}
