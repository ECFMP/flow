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
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'latitude' => $departure->latitude,
            'longitude' => $departure->longitude,
            'groundspeed' => 49,
            'flight_plan' => [
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Ground, $data);
    }

    public function testItReturnsGroundStatusIfAircraftIsTravellingSlow()
    {
        $data = [
            'groundspeed' => 29,
            'latitude' => 0,
            'longitude' => 0,
            'flight_plan' => null,
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Ground, $data);
    }

    public function testItIsDepartingIfCloseToAirportButTravellingFast()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'latitude' => $departure->latitude,
            'longitude' => $departure->longitude,
            'groundspeed' => 50,
            'flight_plan' => [
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Departing, $data);
    }

    public function testItIsDepartingIfCloseToAirportButDepartureAirportHasNoCoordinates()
    {
        $departure = Airport::factory()->create(['latitude' => null, 'longitude' => null]);
        $arrival = Airport::factory()->create();

        $data = [
            'latitude' => 10,
            'longitude' => 11,
            'groundspeed' => 49,
            'flight_plan' => [
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Departing, $data);
    }

    public function testItIsDepartingIfTravellingFast()
    {
        $data = [
            'groundspeed' => 30,
            'latitude' => 0,
            'longitude' => 0,
            'flight_plan' => null,
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Departing, $data);
    }

    public function testItIsDepartingIfArrivalAirportHasNoCoordinates()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create(['latitude' => null, 'longitude' => null]);

        $data = [
            'altitude' => 1,
            'groundspeed' => 51,
            'latitude' => $arrival->latitude,
            'longitude' => $arrival->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Departing, $data);
    }

    public function testItIsCruisingIf4000FeetBelowCruise()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 16001,
            'groundspeed' => 150,
            'latitude' => $departure->latitude,
            'longitude' => $departure->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Cruise, $data);
    }

    public function testItIsCruisingIf4000AboveCruise()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 23999,
            'groundspeed' => 130,
            'latitude' => $departure->latitude,
            'longitude' => $departure->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Cruise, $data);
    }

    public function testItIsCruisingIfAtCruise()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 20000,
            'groundspeed' => 150,
            'latitude' => $departure->latitude,
            'longitude' => $departure->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Cruise, $data);
    }

    public function testItIsCruisingIfCloseToAirfieldButNotDescended()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 20000,
            'groundspeed' => 140,
            'latitude' => $arrival->latitude,
            'longitude' => $arrival->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Cruise, $data);
    }

    public function testItIsDesendingIfBelowCruise()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 15000,
            'groundspeed' => 51,
            'latitude' => $arrival->latitude,
            'longitude' => $arrival->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Descending, $data);
    }

    public function testItIsDesendingIfBelowCruiseAndALongWayFromAirfield()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 15000,
            'groundspeed' => 441,
            // 2 degrees is roughly 120nm
            'latitude' => $arrival->latitude + 2,
            'longitude' => $arrival->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Descending, $data);
    }

    public function testItIsLanded()
    {
        $departure = Airport::factory()->create();
        $arrival = Airport::factory()->create();

        $data = [
            'altitude' => 15,
            'groundspeed' => 49,
            'latitude' => $arrival->latitude,
            'longitude' => $arrival->longitude,
            'flight_plan' => [
                'altitude' => 20000,
                'departure' => $departure->icao_code,
                'arrival' => $arrival->icao_code,
            ],
        ];

        $this->assertPilotStatusIs(VatsimPilotStatus::Landed, $data);
    }

    private function assertPilotStatusIs(VatsimPilotStatus $status, array $data): void
    {
        $this->assertEquals(
            ['vatsim_pilot_status_id' => $status],
            $this->pilotStatus->processPilotData($data, [])
        );
    }
}
