<?php

namespace App\Discord\FlowMeasure\Field\Filters;

use App\Discord\Message\Embed\FieldProviderInterface;

abstract class AbstractFlowMeasureFilterField implements FieldProviderInterface
{
    protected readonly array $filter;

    public function __construct(array $filter)
    {
        $this->filter = $filter;
    }
}
