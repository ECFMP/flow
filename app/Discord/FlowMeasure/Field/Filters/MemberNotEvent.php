<?php

namespace App\Discord\FlowMeasure\Field\Filters;

class MemberNotEvent extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Not Participating in Event';
    }

    public function value(): string
    {
        return $this->eventName();
    }
}
