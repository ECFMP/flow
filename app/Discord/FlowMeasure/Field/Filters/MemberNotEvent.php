<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Models\Event;

class MemberNotEvent extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Not Participating in Event';
    }

    public function value(): string
    {
        return Event::findOrFail($this->filter['value'])->name;
    }
}
