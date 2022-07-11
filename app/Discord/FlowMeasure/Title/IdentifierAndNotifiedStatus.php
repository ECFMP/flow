<?php

namespace App\Discord\FlowMeasure\Title;

use App\Models\FlowMeasure;

class IdentifierAndNotifiedStatus extends AbstractFlowMeasureTitle
{
    private readonly bool $isReissue;

    private function __construct(bool $isReissue, FlowMeasure $flowMeasure)
    {
        parent::__construct($flowMeasure);
        $this->isReissue = $isReissue;
    }

    public static function create(FlowMeasure $measure): IdentifierAndNotifiedStatus
    {
        return new static(false, $measure);
    }

    public static function createReissued(FlowMeasure $measure): IdentifierAndNotifiedStatus
    {
        return new static(true, $measure);
    }

    public function title(): string
    {
        return $this->formatIdentifierStatusAndReissued('Notified', $this->isReissue);
    }
}
