<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\Departing;
use App\Models\AirportGroup;
use App\Models\FlowMeasure;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tests\TestCase;

class DepartingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    public function doCall(FlowMeasure $flowMeasure): string
    {
        return (new Departing($flowMeasure))->toString();
    }

    public function testItThrowsExceptionIfNoDepartingAirports()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one departure airport');

        $measure = FlowMeasure::factory()->create();
        $measure->filters = [];

        $this->doCall($measure);
    }

    public function testItThrowsExceptionIfTooManyFilters()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one departure airport');

        $measure = FlowMeasure::factory()->create();
        $measure->filters = [
            [
                'type' => 'ADES',
                'value' => 'EGKK',
            ],
            [
                'type' => 'ADES',
                'value' => 'EGLL',
            ],
        ];

        $this->doCall($measure);
    }

    public function testItReturnsAirportString()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals('ADEP: EG**', $this->doCall($measure));
    }

    public function testItReturnsAirportStringWithAGroup()
    {
        $group = AirportGroup::factory()->create(['name' => 'Severn Clutch']);
        $measure = FlowMeasure::factory()->withDepartureAirports(['EGKK', $group->id])->create();

        $this->assertEquals('ADEP: EGKK, ' . $group->name, $this->doCall($measure));
    }
}
