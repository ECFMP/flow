<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\Message\Content\ContentInterface;
use App\Models\FlowMeasure;

abstract class AbstractFlowMeasureContent implements ContentInterface
{
    protected readonly FlowMeasure $flowMeasure;

    public function __construct(FlowMeasure $flowMeasure)
    {
        $this->flowMeasure = $flowMeasure;
    }
}
