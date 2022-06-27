<?php

namespace App\Discord\FlowMeasure\Title;

use App\Models\FlowMeasure;

class IdentifierAndActiveStatus extends AbstractFlowMeasureTitle
{
    private readonly bool $isReissue;

    private function __construct(bool $isReissue, FlowMeasure $flowMeasure)
    {
        parent::__construct($flowMeasure);
        $this->isReissue = $isReissue;
    }

    public static function create(FlowMeasure $measure): IdentifierAndActiveStatus
    {
        return new static(false, $measure);
    }

    public static function createReissued(FlowMeasure $measure): IdentifierAndActiveStatus
    {
        return new static(true, $measure);
    }

    public function title(): string
    {
        return $this->formatIdentifierStatusAndReissued('Active', $this->isReissue);
    }
}
