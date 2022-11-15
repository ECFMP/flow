<?php

namespace App\Vatsim\Processor\Pilot;

class PilotStatus implements PilotDataSubprocessorInterface
{
    use ChecksFlightplanFields;

    public function processPilotData(array $data): array
    {
        return ['vatsim_pilot_status_id' => 0];
    }

    public function generatePilotStatus(array $data): int
    {

    }
}
