<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Models\Event;

class MemberEvent extends AbstractFlowMeasureFilterField
{

    public function name(): string
    {
        return 'Participating in Event';
    }

    public function value(): string
    {
        return Event::findOrFail($this->filter['value'])->name;
    }
}
