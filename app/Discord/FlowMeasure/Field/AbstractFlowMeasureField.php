<?php

namespace App\Discord\FlowMeasure\Field;

use App\Discord\Message\Embed\FieldProviderInterface;
use App\Models\FlowMeasure;

abstract class AbstractFlowMeasureField implements FieldProviderInterface
{
    protected readonly FlowMeasure $flowMeasure;

    public function __construct(FlowMeasure $flowMeasure)
    {
        $this->flowMeasure = $flowMeasure;
    }
}
