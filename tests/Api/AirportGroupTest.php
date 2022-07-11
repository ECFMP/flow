<?php

namespace Tests\Api;

use App\Models\Airport;
use App\Models\AirportGroup;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AirportGroupTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('airports')->delete();
        DB::table('airport_groups')->delete();
    }

    public function testItReturns404IfGroupNotFound()
    {
        $this->get('api/v1/airport-group/55')
            ->assertNotFound();
    }

    public function testItReturnsAirportGroupById()
    {
        $group = AirportGroup::factory()
            ->has(Airport::factory()->count(3))
            ->create();

        $this->get('api/v1/airport-group/' . $group->id)
            ->assertOk()
            ->assertExactJson([
                'id' => $group->id,
                'name' => $group->name,
                'airports' => $group->airports->pluck('icao_code')->toArray()
            ]);
    }

    public function testItReturnsEmptyIfNoAirportGroups()
    {
        $this->get('api/v1/airport-group')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function testItReturnsAirportGroups()
    {
        $group1 = AirportGroup::factory()
            ->has(Airport::factory()->count(3))
            ->create();

        $group2 = AirportGroup::factory()
            ->has(Airport::factory()->count(4))
            ->create();

        $this->get('api/v1/airport-group')
            ->assertOk()
            ->assertExactJson([
                [
                    'id' => $group1->id,
                    'name' => $group1->name,
                    'airports' => $group1->airports->pluck('icao_code')->toArray()
                ],
                [
                    'id' => $group2->id,
                    'name' => $group2->name,
                    'airports' => $group2->airports->pluck('icao_code')->toArray()
                ],
            ]);
    }
}
