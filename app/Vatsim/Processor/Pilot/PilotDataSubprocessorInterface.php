<?php

namespace App\Vatsim\Processor\Pilot;

interface PilotDataSubprocessorInterface
{
    /**
     * Applies processing to the pilot data array and returns the result of that processing.
     * 
     * This method can be used, for example, to add some calculated data that can't be grabbed straight from the VATSIM
     * data feed, for example, time until arrival.
     */
    public function processPilotData(array $data): array;
}
