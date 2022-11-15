<?php

namespace Tests\Vatsim\Processor\Pilot;

use App\Models\VatsimPilot;
use App\Vatsim\Processor\PilotProcessor;
use Carbon\Carbon;
use Tests\TestCase;

class PilotProcessorTest extends TestCase
{
    public function testItProcessesPilotData()
    {
        Carbon::setTestNow(Carbon::now()->startOfSecond());

        $existingAircraft = VatsimPilot::factory()->create(['callsign' => 'RYR789']);
        $existingAircraftNotTimedOut = VatsimPilot::factory()->create(['callsign' => 'DONTDELETEME']);
        $existingAircraftNotTimedOut->updated_at = Carbon::now()->subMinutes(15)->addSecond();
        $existingAircraftNotTimedOut->save();
        $existingAircraftToDelete = VatsimPilot::factory()->create(['callsign' => 'DELETEME']);
        $existingAircraftToDelete->updated_at = Carbon::now()->subMinutes(15)->subSecond();
        $existingAircraftToDelete->save();

        $networkData = [
            'pilots' => [
                // Should be created
                [
                    'callsign' => 'BAW123',
                    'cid' => 1234,
                    'altitude' => 23123,
                    'flight_plan' => [
                        'departure' => 'EGGD',
                        'arrival' => 'EDDM',
                        'altitude' => 35000,
                        'route' => 'ABC DEF'
                    ],
                ],
                // Should be created, with NULLable data
                [
                    'callsign' => 'EZY456',
                    'cid' => 2345,
                    'altitude' => 31126,
                    'flight_plan' => null,
                ],
                // Already exists, should get updated
                [
                    'callsign' => 'RYR789',
                    'cid' => 35261,
                    'altitude' => 23521,
                    'flight_plan' => [
                        'departure' => 'EGKK',
                        'arrival' => 'EHAM',
                        'altitude' => 23000,
                        'route' => 'DEF GHI'
                    ],
                ],
            ]
        ];

        $processor = $this->app->make(PilotProcessor::class);
        $processor->processNetworkData($networkData);

        // BAW123 is new in the data, should be created
        $this->assertDatabaseHas(
            'vatsim_pilots',
            [
                'callsign' => 'BAW123',
                'cid' => 1234,
                'altitude' => 23123,
                'departure_airport' => 'EGGD',
                'destination_airport' => 'EDDM',
                'cruise_altitude' => 35000,
                'route_string' => 'ABC DEF'
            ]
        );
        $baw123 = VatsimPilot::where('callsign', 'BAW123')->firstOrFail();
        $this->assertGreaterThan(Carbon::now()->subSeconds(5), $baw123->updated_at);

        // EZY456 is new in the data, but hasn't filed a flightplan
        $this->assertDatabaseHas(
            'vatsim_pilots',
            [
                'callsign' => 'EZY456',
                'cid' => 2345,
                'altitude' => 31126,
                'departure_airport' => null,
                'destination_airport' => null,
                'cruise_altitude' => null,
                'route_string' => null,
            ]
        );
        $ezy456 = VatsimPilot::where('callsign', 'EZY456')->firstOrFail();
        $this->assertGreaterThan(Carbon::now()->subSeconds(5), $ezy456->updated_at);

        // RYR789 is in the data already, so should be updated
        $this->assertDatabaseHas(
            'vatsim_pilots',
            [
                'id' => $existingAircraft->id,
                'callsign' => 'RYR789',
                'cid' => 35261,
                'altitude' => 23521,
                'departure_airport' => 'EGKK',
                'destination_airport' => 'EHAM',
                'cruise_altitude' => 23000,
                'route_string' => 'DEF GHI',
            ]
        );
        $existingAircraft->refresh();
        $this->assertGreaterThan(Carbon::now()->subSeconds(5), $existingAircraft->updated_at);

        // This aircraft is not in the data, but yet to time out
        $this->assertDatabaseHas(
            'vatsim_pilots',
            [
                'id' => $existingAircraftNotTimedOut->id,
                'callsign' => $existingAircraftNotTimedOut->callsign,
                'altitude' => $existingAircraftNotTimedOut->altitude
            ]
        );
        $existingAircraftNotTimedOut->refresh();
        $this->assertEquals(Carbon::now()->subMinutes(15)->addSecond(), $existingAircraftNotTimedOut->updated_at);

        // This aircraft has timed out, we shouldn't see it
        $this->assertDatabaseMissing(
            'vatsim_pilots',
            [
                'id' => $existingAircraftToDelete->id,
            ]
        );
    }
}
