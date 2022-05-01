<?php

namespace Tests\Unit;

use App\Helpers\FlowMeasureFilterApiFormatter;
use App\Models\Airport;
use App\Models\AirportGroup;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlowMeasureFilterApiFormatterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('airports')->delete();
        DB::table('airport_groups')->delete();
    }

    public function testItReturnsStringAirports()
    {
        $this->assertEquals(
            ['EGKK', 'EGP*'],
            FlowMeasureFilterApiFormatter::formatAirportList(['EGKK', 'EGP*'])
        );
    }

    public function testItReturnsAirportGroupsByIcao()
    {
        $airportGroup = AirportGroup::factory()
            ->has(Airport::factory()->count(5))
            ->create();

        $this->assertEquals(
            $airportGroup->airports->pluck('icao_code')->sort()->values()->toArray(),
            FlowMeasureFilterApiFormatter::formatAirportList([$airportGroup->id])
        );
    }

    public function testItCombinesSpecificAirfieldsAndGroups()
    {
        $airportGroup = AirportGroup::factory()
            ->has(Airport::factory()->count(5))
            ->create();

        $this->assertEquals(
            collect(['ZZZZ'])->concat($airportGroup->airports->pluck('icao_code'))->sort()->values()->toArray(),
            FlowMeasureFilterApiFormatter::formatAirportList(['ZZZZ', $airportGroup->id])
        );
    }

    public function testItCombinesMultipleGroups()
    {
        $airportGroup1 = AirportGroup::factory()
            ->has(Airport::factory()->count(5))
            ->create();

        $airportGroup2 = AirportGroup::factory()
            ->has(Airport::factory()->count(2))
            ->create();

        $this->assertEquals(
            $airportGroup1->airports->pluck('icao_code')->concat($airportGroup2->airports->pluck('icao_code'))->sort(
            )->values()->toArray(),
            FlowMeasureFilterApiFormatter::formatAirportList([$airportGroup1->id, $airportGroup2->id])
        );
    }

    public function testItRemovesDuplicates()
    {
        $airportGroup = AirportGroup::factory()
            ->has(Airport::factory()->count(5))
            ->create();

        $this->assertEquals(
            $airportGroup->airports->pluck('icao_code')->sort()->values()->toArray(),
            FlowMeasureFilterApiFormatter::formatAirportList(
                [$airportGroup->id, $airportGroup->id]
            )
        );
    }
}
