<?php

namespace Tests\Unit;

use App\Discord\FlowMeasure\Content\OtherFilters;
use App\Models\Event;
use App\Models\FlowMeasure;
use DB;
use Tests\TestCase;

class OtherFiltersContentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    public function getContent(FlowMeasure $flowMeasure): string
    {
        return (new OtherFilters($flowMeasure))->toString();
    }

    public function testItReturnsEmptyIfNoAdditionalFilters()
    {
        $this->assertEquals(
            '',
            $this->getContent(FlowMeasure::factory()->create())
        );
    }

    public function testItReturnsWaypointFilter()
    {
        $this->assertEquals(
            'VIA WPT: TEST',
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters([['type' => 'waypoint', 'value' => 'TEST']])->create()
            )
        );
    }

    public function testItReturnsLevelAboveFilter()
    {
        $this->assertEquals(
            'LVL ABV: 290',
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters([['type' => 'level_above', 'value' => 290]])->create()
            )
        );
    }

    public function testItReturnsLevelBelowFilter()
    {
        $this->assertEquals(
            'LVL BLW: 290',
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters([['type' => 'level_below', 'value' => 290]])->create()
            )
        );
    }

    public function testItReturnsLevelFilter()
    {
        $this->assertEquals(
            'LVL: 290',
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters([['type' => 'level', 'value' => 290]])->create()
            )
        );
    }

    public function testItReturnsMemberEventFilter()
    {
        $event = Event::factory()->create();

        $this->assertEquals(
            'EVENT: ' . $event->name,
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters([['type' => 'member_event', 'value' => $event->id]]
                )->create()
            )
        );
    }

    public function testItReturnsMemberNotEventFilter()
    {
        $event = Event::factory()->create();

        $this->assertEquals(
            'NON EVENT: ' . $event->name,
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters([['type' => 'member_not_event', 'value' => $event->id]]
                )->create()
            )
        );
    }

    public function testItCombinesFilters()
    {
        $event = Event::factory()->create();

        $this->assertEquals(
            'LVL: 290' . PHP_EOL . 'NON EVENT: ' . $event->name,
            $this->getContent(
                FlowMeasure::factory()->withAdditionalFilters(
                    [['type' => 'level', 'value' => 290], ['type' => 'member_not_event', 'value' => $event->id]]
                )->create()
            )
        );
    }
}
