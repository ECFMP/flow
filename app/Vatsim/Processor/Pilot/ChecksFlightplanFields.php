<?php

namespace App\Vatsim\Processor\Pilot;

use Illuminate\Support\Arr;

trait ChecksFlightplanFields
{
    private function getFlightplanItem(array $pilot, string $item): mixed
    {
        return Arr::get($pilot, sprintf('flight_plan.%s', $item));
    }
}
