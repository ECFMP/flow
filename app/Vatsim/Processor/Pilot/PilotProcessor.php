<?php

namespace App\Vatsim\Processor\Pilot;

use App\Models\VatsimPilot;
use App\Vatsim\Processor\VatsimDataProcessorInterface;
use Carbon\Carbon;
use DB;

class PilotProcessor implements VatsimDataProcessorInterface
{
    use ChecksFlightplanFields;

    private const STALE_PILOT_TIMEOUT_MINUTES = 15;

    private readonly array $subprocessors;

    /**
     * @param PilotDataSubprocessorInterface[] $subprocessors
     */
    public function __construct(array $subprocessors)
    {
        $this->subprocessors = $subprocessors;
    }

    /**
     * Function for processing network data.
     *
     * @param array $data
     */
    public function processNetworkData(array $data): void
    {
        DB::transaction(function () use ($data) {
            $this->updatePilots($data);
            $this->removeStalePilots();
        });
    }

    private function updatePilots(array $data): void
    {
        VatsimPilot::upsert(
            array_map(
                fn (array $pilot): array => array_reduce(
                    $this->subprocessors,
                    fn (array $formattedData, PilotDataSubprocessorInterface $processor) =>
                    array_merge($formattedData, $processor->processPilotData($pilot, $formattedData)),
                    [
                        'callsign' => $pilot['callsign'],
                        'cid' => $pilot['cid'],
                        'departure_airport' => $this->getFlightplanItem($pilot, 'departure'),
                        'destination_airport' => $this->getFlightplanItem($pilot, 'arrival'),
                        'altitude' => (int) $pilot['altitude'],
                        'cruise_altitude' => is_null($cruise = $this->getFlightplanItem($pilot, 'altitude'))
                        ? null
                        : (int) $cruise,
                        'route_string' => $this->getFlightplanItem($pilot, 'route'),
                    ]
                ),
                $data['pilots']
            ),
            ['callsign']
        );
    }

    private function removeStalePilots(): void
    {
        VatsimPilot::where('updated_at', '<', Carbon::now()->subMinutes(self::STALE_PILOT_TIMEOUT_MINUTES))
            ->delete();
    }
}
