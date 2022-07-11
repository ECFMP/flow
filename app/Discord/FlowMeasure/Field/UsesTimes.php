<?php

namespace App\Discord\FlowMeasure\Field;

use Carbon\CarbonInterface;

trait UsesTimes
{
    private static function shortTime(CarbonInterface $time): string
    {
        return $time->format('Hi') . 'Z';
    }

    private static function dateTime(CarbonInterface $time): string
    {
        return $time->format('d/m Hi') . 'Z';
    }
}
