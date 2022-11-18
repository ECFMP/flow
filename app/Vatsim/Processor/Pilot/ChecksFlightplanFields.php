<?php

namespace App\Vatsim\Processor\Pilot;

use Illuminate\Support\Arr;

trait ChecksFlightplanFields
{
    private function getFlightplanItem(array $pilot, string $item): mixed
    {
        return Arr::get($this->getFlightplan($pilot), $item);
    }

    private function getFlightplan(array $pilot): ?array
    {
        return Arr::get($pilot, 'flight_plan');
    }

    private function hasFlightplan(array $pilot): bool
    {
        return !is_null($this->getFlightplan($pilot));
    }
}
