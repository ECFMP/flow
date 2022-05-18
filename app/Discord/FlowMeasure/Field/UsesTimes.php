<?php

namespace App\Discord\FlowMeasure\Field;

use Carbon\CarbonInterface;

trait UsesTimes
{
    private static function shortTime(CarbonInterface $time): string
    {
        return $time->format('HiZ');
    }

    private static function dateTime(CarbonInterface $time): string
    {
        return $time->format('d/m HiZ');
    }
}
