<?php

namespace Tests\Vatsim\Processor\Pilot;

use App\Models\Airport;
use App\Models\VatsimPilot;
use App\Models\VatsimPilotStatus;
use App\Vatsim\Processor\Pilot\EstimatedArrivalTime;
use Carbon\Carbon;
use LogicException;
use Tests\TestCase;

class EstimatedArrivalTimeTest extends TestCase
{
    private readonly EstimatedArrivalTime $estimatedArrivalTime;

    public function setUp(): void
    {
        parent::setUp();
        $this->estimatedArrivalTime = $this->app->make(EstimatedArrivalTime::class);
        Carbon::setTestNow(Carbon::parse('2022-11-18 17:33:00'));
    }

    public function testItThrowsExceptionIfNoPilotStatus()
    {
        $this->expectException(LogicException::class);
        $data = [
            'flight_plan' => null
        ];

        $this->estimatedArrivalTime->processPilotData($data, []);
    }


    public function testItReturnsNullIfNoFlightplan()
    {
        $data = [
            'flight_plan' => null
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Ground,
        ];

        $this->assertEquals(['estimated_arrival_time' => null], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItReturnsNullIfOnTheGround()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $data = [
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => 'LOLOLOL',
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Ground,
        ];

        $this->assertEquals(['estimated_arrival_time' => null], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItReturnsNullIfAirportNotFound()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create();

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => 'LOLOLOL',
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Cruise,
        ];

        $this->assertEquals(['estimated_arrival_time' => null], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItReturnsNullIfAirportHasNoLatitude()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => null, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create();

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Cruise,
        ];

        $this->assertEquals(['estimated_arrival_time' => null], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }


    public function testItReturnsNullIfAirportHasNoLongitude()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => null]);
        $pilot = VatsimPilot::factory()->create();

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Cruise,
        ];

        $this->assertEquals(['estimated_arrival_time' => null], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItReturnsEstimatedForCruise()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create();

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Cruise,
        ];

        $this->assertEquals(['estimated_arrival_time' => Carbon::parse('2022-11-18 17:51:00')], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItReturnsEstimatedForDescent()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create();

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Descending,
        ];

        $this->assertEquals(['estimated_arrival_time' => Carbon::parse('2022-11-18 17:51:00')], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItReturnsEstimatedForDeparting()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create();

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Departing,
        ];

        $this->assertEquals(['estimated_arrival_time' => Carbon::parse('2022-11-18 17:57:00')], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItSetsCurrentTimeIfLanded()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create(['vatsim_pilot_status_id' => VatsimPilotStatus::Descending]);

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Landed,
        ];

        $this->assertEquals(['estimated_arrival_time' => Carbon::now()], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItSetsCurrentTimeIfLandedNoPilot()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45,]);

        $data = [
            'callsign' => 'BAW123',
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Landed,
        ];

        $this->assertEquals(['estimated_arrival_time' => Carbon::now()], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }

    public function testItKeepsLandedTimeIfLanded()
    {
        $arrivalAirport = Airport::factory()->create(['latitude' => 1.23, 'longitude' => 3.45]);
        $pilot = VatsimPilot::factory()->create(['estimated_arrival_time' => Carbon::now()->subMinutes(5), 'vatsim_pilot_status_id' => VatsimPilotStatus::Landed]);

        $data = [
            'callsign' => $pilot->callsign,
            'groundspeed' => 400,
            'latitude' => $arrivalAirport->latitude + 2,
            'longitude' => $arrivalAirport->longitude,
            'flight_plan' => [
                'arrival' => $arrivalAirport->icao_code,
                'cruise_tas' => 300,
            ]
        ];

        $formattedData = [
            'vatsim_pilot_status_id' => VatsimPilotStatus::Landed,
        ];

        $this->assertEquals(['estimated_arrival_time' => Carbon::now()->subMinutes(5)], $this->estimatedArrivalTime->processPilotData($data, $formattedData));
    }
}
