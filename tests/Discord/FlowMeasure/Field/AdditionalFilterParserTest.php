<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\AdditionalFilterParser;
use App\Discord\FlowMeasure\Field\Filters\Level;
use App\Discord\FlowMeasure\Field\Filters\LevelAbove;
use App\Discord\FlowMeasure\Field\Filters\LevelBelow;
use App\Discord\FlowMeasure\Field\Filters\MemberEvent;
use App\Discord\FlowMeasure\Field\Filters\MemberNotEvent;
use App\Discord\FlowMeasure\Field\Filters\ViaWaypoint;
use App\Models\FlowMeasure;
use Tests\TestCase;

class AdditionalFilterParserTest extends TestCase
{
    public function testItParsesWaypointFilter()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilter(
            [
                'type' => 'waypoint',
                'value' => ['abc'],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(ViaWaypoint::class, $collection->first());
    }

    public function testItParsesLevelFilter()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilter(
            [
                'type' => 'level',
                'value' => [123],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Level::class, $collection->first());
    }

    public function testItParsesLevelAboveFilter()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilter(
            [
                'type' => 'level_above',
                'value' => [123],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(LevelAbove::class, $collection->first());
    }

    public function testItParsesLevelBelowFilter()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilter(
            [
                'type' => 'level_below',
                'value' => [123],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(LevelBelow::class, $collection->first());
    }

    public function testItParsesMemberEventFilter()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilter(
            [
                'type' => 'member_event',
                'value' => [123],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(MemberEvent::class, $collection->first());
    }

    public function testItParsesMemberNotEventFilter()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilter(
            [
                'type' => 'member_not_event',
                'value' => [123],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(MemberNotEvent::class, $collection->first());
    }

    public function testItParsesMultipleFilters()
    {
        $measure = FlowMeasure::factory()->withAdditionalFilters(
            [
                [
                    'type' => 'level_above',
                    'value' => [123],
                ],
                [
                    'type' => 'member_not_event',
                    'value' => [123],
                ],
            ]
        )->create();

        $collection = AdditionalFilterParser::parseAdditionalFilters($measure);
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains(fn($item) => $item instanceof LevelAbove));
        $this->assertTrue($collection->contains(fn($item) => $item instanceof MemberNotEvent));
    }
}
