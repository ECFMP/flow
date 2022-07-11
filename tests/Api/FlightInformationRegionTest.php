<?php

namespace Tests\Api;

use App\Models\FlightInformationRegion;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlightInformationRegionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
        DB::table('events')->delete();
        DB::table('flight_information_regions')->delete();
    }

    public function testItReturnsNotFoundIfFlightInformationRegionDoesNotExist()
    {
        $this->get('api/v1/flight-information-region/55')
            ->assertNotFound();
    }

    public function testItReturnsAFlightInformationRegion()
    {
        $fir = FlightInformationRegion::factory()->create();
        $this->get('api/v1/flight-information-region/' . $fir->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $fir->id,
                'identifier' => $fir->identifier,
                'name' => $fir->name,
            ]);
    }

    public function testItReturnsEmptyIfThereAreNoFlightInformationRegions()
    {
        $this->get('api/v1/flight-information-region')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItReturnsAllTheFlightInformationRegions()
    {
        $fir1 = FlightInformationRegion::factory()->create();
        $fir2 = FlightInformationRegion::factory()->create();
        $this->get('api/v1/flight-information-region')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $fir1->id,
                    'identifier' => $fir1->identifier,
                    'name' => $fir1->name,
                ],
                [
                    'id' => $fir2->id,
                    'identifier' => $fir2->identifier,
                    'name' => $fir2->name,
                ],
            ]);
    }
}
