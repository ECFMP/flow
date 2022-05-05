<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Content\Arriving;
use App\Models\AirportGroup;
use App\Models\FlowMeasure;
use DB;
use InvalidArgumentException;
use Tests\TestCase;

class ArrivingFlowMeasureContentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    public function doCall(FlowMeasure $flowMeasure): string
    {
        return (new Arriving($flowMeasure))->toString();
    }

    public function testItThrowsExceptionIfNoArrivingAirports()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one arrival airport');

        $measure = FlowMeasure::factory()->create();
        $measure->filters = [];

        $this->doCall($measure);
    }

    public function testItThrowsExceptionIfTooManyFilters()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one arrival airport');

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
        $this->assertEquals('DEST: EHAM', $this->doCall($measure));
    }

    public function testItReturnsAirportStringWithAGroup()
    {
        $group = AirportGroup::factory()->create(['name' => 'Severn Clutch']);
        $measure = FlowMeasure::factory()->withArrivalAirports(['EGKK', $group->id])->create();

        $this->assertEquals('DEST: EGKK, ' . $group->name, $this->doCall($measure));
    }
}
