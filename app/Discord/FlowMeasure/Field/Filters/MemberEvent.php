<?php

namespace App\Discord\FlowMeasure\Field\Filters;

class MemberEvent extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Participating in Event';
    }

    public function value(): string
    {
        return $this->eventName();
    }
}
