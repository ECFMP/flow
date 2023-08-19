<?php

namespace Tests\Vatsim\Processor\Pilot;

use App\Models\Airport;
use App\Vatsim\Processor\Pilot\DistanceToDestination;
use Tests\TestCase;

class DistanceToDestinationTest extends TestCase
{
    private readonly DistanceToDestination $distance;

    public function setUp(): void
    {
        parent::setUp();
        $this->distance = $this->app->make(DistanceToDestination::class);
    }

    public function testItReturnsNullIfNoArrivalAirport()
    {
        $data = [
            'flight_plan' => null,
        ];

        $this->assertEquals(['distance_to_destination' => null], $this->distance->processPilotData($data, []));
    }

    public function testItReturnsNullIfArrivalAirportNotFound()
    {
        $data = [
            'flight_plan' => [
                'arrival' => 'XYXY',
            ],
        ];

        $this->assertEquals(['distance_to_destination' => null], $this->distance->processPilotData($data, []));
    }

    public function testItReturnsNullIfArrivalAirportHasNoLatitude()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => null]);
        $data = [
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
            ],
        ];

        $this->assertEquals(['distance_to_destination' => null], $this->distance->processPilotData($data, []));
    }

    public function testItReturnsNullIfArrivalAirportHasNoLongitude()
    {
        $arrivalAirport = Airport::factory()->create(['longitude' => null]);
        $data = [
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
            ],
        ];

        $this->assertEquals(['distance_to_destination' => null], $this->distance->processPilotData($data, []));
    }

    public function testItReturnsDistanceToDestination()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 4.56]);
        $data = [
            'latitude' => 3.45,
            'longitude' => 4.56,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
            ],
        ];

        $this->assertEqualsWithDelta(133.29, $this->distance->processPilotData($data, [])['distance_to_destination'], 0.01);
    }
}
