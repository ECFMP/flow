<?php

namespace App\Repository\FlowMeasureNotification;

use App\Models\FlowMeasure;

readonly class FlowMeasureForNotification
{
    public function __construct(
        public FlowMeasure $measure,
        public bool $isReissuedNotification,
    ) {
    }
}
