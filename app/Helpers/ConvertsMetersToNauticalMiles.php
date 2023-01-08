<?php

namespace App\Helpers;

trait ConvertsMetersToNauticalMiles
{
    private function metersToNauticalMiles(float $meters): float
    {
        return $meters * 0.000539957;
    }
}
