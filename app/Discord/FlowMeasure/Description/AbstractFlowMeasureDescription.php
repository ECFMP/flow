<?php

namespace App\Discord\FlowMeasure\Description;

use App\Discord\Message\Embed\DescriptionInterface;
use App\Models\FlowMeasure;

abstract class AbstractFlowMeasureDescription implements DescriptionInterface
{
    protected readonly FlowMeasure $flowMeasure;

    public function __construct(FlowMeasure $flowMeasure)
    {
        $this->flowMeasure = $flowMeasure;
    }
}
