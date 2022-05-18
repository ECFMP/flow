<?php

namespace App\Discord\FlowMeasure\Footer;

use App\Discord\Message\Embed\FooterInterface;
use App\Models\FlowMeasure;

abstract class AbstractFlowMeasureFooter implements FooterInterface
{
    protected readonly FlowMeasure $flowMeasure;

    public function __construct(FlowMeasure $flowMeasure)
    {
        $this->flowMeasure = $flowMeasure;
    }
}
