<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\DepartureAirports;
use App\Models\AirportGroup;
use App\Models\FlowMeasure;
use InvalidArgumentException;
use Tests\TestCase;

class DepartureAirportsTest extends TestCase
{
    private function getField(FlowMeasure $flowMeasure): DepartureAirports
    {
        return new DepartureAirports(($flowMeasure));
    }

    public function testItHasAName()
    {
        $measure = FlowMeasure::factory()->create();

        $this->assertEquals(
            'Departure Airports',
            $this->getField($measure)->name()
        );
    }

    public function testItThrowsExceptionIfNoArrivingAirports()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one departure airport');

        $measure = FlowMeasure::factory()->create();
        $measure->filters = [];

        $this->getField($measure)->value();
    }

    public function testItThrowsExceptionIfTooManyFilters()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one departure airport');

        $measure = FlowMeasure::factory()->create();
        $measure->filters = [
            [
                'type' => 'ADEP',
                'value' => 'EGKK',
            ],
            [
                'type' => 'ADEP',
                'value' => 'EGLL',
            ],
        ];

        $this->getField($measure)->value();
    }

    public function testItReturnsAirportString()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals('EG**', $this->getField($measure)->value());
    }

    public function testItReturnsAirportStringWithAGroup()
    {
        $group = AirportGroup::factory()->create(['name' => 'Severn Clutch']);
        $measure = FlowMeasure::factory()->withDepartureAirports(['EGKK', $group->id])->create();

        $this->assertEquals('EGKK, Severn Clutch', $this->getField($measure)->value());
    }
}
