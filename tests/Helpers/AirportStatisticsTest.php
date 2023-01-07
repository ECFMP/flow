<?php

namespace App\Helpers;

use App\Models\Airport;
use App\Models\VatsimPilot;
use Carbon\Carbon;
use Tests\TestCase;

class AirportStatisticsTest extends TestCase
{
    private readonly AirportStatistics $statistics;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfMinute());
        $this->statistics = $this->app->make(AirportStatistics::class);
    }

    public function testItReturnsTotalInbound()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')->create();
        VatsimPilot::factory()->destination($airport)->landedMinutesAgo(6)->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)->create();
        VatsimPilot::factory()->destination($airport)->create();
        VatsimPilot::factory()->destination($airport)->create();
        VatsimPilot::factory()->destination($airport)->onTheGround()->create();
        VatsimPilot::factory()->destination($airport)->landedMinutesAgo(4)->create();

        $this->assertEquals(5, $this->statistics->getTotalInbound($airport->id));
    }

    public function testItReturnsLandedLastTenMinutes()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')->landedMinutesAgo(5)->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->subMinutes(5))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->descending()
            ->withEstimatedArrivalTime(Carbon::now()->subMinutes(5))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->withEstimatedArrivalTime(Carbon::now()->subMinutes(5))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->withEstimatedArrivalTime(Carbon::now()->subMinutes(5))
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)->landedMinutesAgo(9)->create();
        VatsimPilot::factory()->destination($airport)->landedMinutesAgo(5)->create();
        VatsimPilot::factory()->destination($airport)->landedMinutesAgo(0)->create();

        $this->assertEquals(3, $this->statistics->getLandedLast10Minutes($airport->id));
    }

    public function testItReturnsInbound30Minutes()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(5))
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->landed()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(5))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(5))
            ->create();

        // Should not be included, too far away
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(31))
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(5))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->descending()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(10))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(20))
            ->create();

        $this->assertEquals(3, $this->statistics->getInbound30Minutes($airport->id));
    }

    public function testItReturnsInbound60Minutes()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(45))
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->landed()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(45))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(45))
            ->create();

        // Should not be included, outside timeframe
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(61))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(29))
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(31))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->descending()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(35))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(59))
            ->create();

        $this->assertEquals(3, $this->statistics->getInbound30To60Minutes($airport->id));
    }

    public function testItReturnsInbound120Minutes()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->landed()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        // Should not be included, outside timeframe
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(59))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(120))
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(60))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->descending()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(119))
            ->create();

        $this->assertEquals(3, $this->statistics->getInbound60To120Minutes($airport->id));
    }

    public function testItReturnsAwaitingDeparture()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->onTheGround()
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->descending()
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->create();

        $this->assertEquals(3, $this->statistics->getAwaitingDeparture($airport->id));
    }

    public function testItReturnsDepartingNearby()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->departing()
            ->distanceToDestination(100)
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(100)
            ->onTheGround()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(100)
            ->descending()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(100)
            ->cruising()
            ->create();

        // Should not be included, too far
        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(401)
            ->departing()
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->distanceToDestination(10)
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->distanceToDestination(100)
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->distanceToDestination(200)
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->departing()
            ->distanceToDestination(399)
            ->create();

        $this->assertEquals(4, $this->statistics->getDepartingNearby($airport->id));
    }

    public function testItReturnsGroundNearby()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->departing()
            ->distanceToDestination(100)
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(100)
            ->departing()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(100)
            ->descending()
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(100)
            ->cruising()
            ->create();

        // Should not be included, too far
        VatsimPilot::factory()->destination($airport)
            ->distanceToDestination(401)
            ->onTheGround()
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->distanceToDestination(10)
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->distanceToDestination(100)
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->distanceToDestination(200)
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->distanceToDestination(399)
            ->create();

        $this->assertEquals(4, $this->statistics->getGroundNearby($airport->id));
    }

    public function testItReturnsAircraftSortedIntoGraphGroups()
    {
        $airport = Airport::factory()->create();

        // Should not be included, different ICAO
        VatsimPilot::factory()->destination('1234')
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        // Should not be included, wrong status
        VatsimPilot::factory()->destination($airport)
            ->landed()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->onTheGround()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(100))
            ->create();

        // Should not be included, outside timeframe
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(121))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(125))
            ->create();

        // Should be included
        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(22))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(29))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(30))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(35))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(53))
            ->create();

        VatsimPilot::factory()->destination($airport)
            ->cruising()
            ->withEstimatedArrivalTime(Carbon::now()->addMinutes(112))
            ->create();

        $expected = [
            30 => 2,
            60 => 3,
            90 => 0,
            120 => 1,
        ];

        $this->assertEquals($expected, $this->statistics->getInboundGraphData($airport->id)->toArray());
    }
}
