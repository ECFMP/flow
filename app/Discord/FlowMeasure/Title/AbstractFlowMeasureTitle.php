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
}
