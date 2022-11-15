<?php

namespace Tests\Vatsim\Processor\Pilot;

use App\Models\Airport;
use App\Models\VatsimPilotStatus;
use App\Vatsim\Processor\Pilot\PilotStatus;
use Tests\TestCase;

class PilotStatusTest extends TestCase
{
    private readonly PilotStatus $pilotStatus;

    public function setUp(): void
    {
        parent::setUp();
        $this->pilotStatus = $this->app->make(PilotStatus::class);
    }

    public function testItReturnsGroundStatusIfAircraftIsCloseToDepartureAirport()
    {
        $airport = Airport::factory()->create();

        $data = [
            'latitude' => $airport->latitude,
            'longitude' => $airport->longitude,
            'flightplan' => [
                'departure' => $airport->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Ground, $data);
    }

    private function assertPilotStatusIs(VatsimPilotStatus $status, array $data): void
    {
        $this->assertEquals(
            ['vatsim_pilot_status_id' => $status],
                $this->pilotStatus->generatePilotStatus($data)
        );
    }
}
