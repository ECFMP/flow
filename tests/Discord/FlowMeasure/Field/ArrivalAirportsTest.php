<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\ArrivalAirports;
use App\Models\Airport;
use App\Models\AirportGroup;
use App\Models\FlowMeasure;
use InvalidArgumentException;
use Tests\TestCase;

class ArrivalAirportsTest extends TestCase
{
    private function getField(FlowMeasure $flowMeasure): ArrivalAirports
    {
        return new ArrivalAirports(($flowMeasure));
    }

    public function testItHasAName()
    {
        $measure = FlowMeasure::factory()->create();

        $this->assertEquals(
            'Arrival Airports',
            $this->getField($measure)->name()
        );
    }

    public function testItThrowsExceptionIfNoArrivingAirports()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must have at least one arrival airport');

        $measure = FlowMeasure::factory()->create();
        $measure->filters = [];

        $this->getField($measure)->value();
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

        $this->getField($measure)->value();
    }

    public function testItReturnsAirportString()
    {
        $measure = FlowMeasure::factory()->create();
        $this->assertEquals('EHAM', $this->getField($measure)->value());
    }

    public function testItReturnsAirportStringWithAGroup()
    {
        $group = AirportGroup::factory()->create(['name' => 'Severn Clutch']);
        $airport1 = Airport::factory()->create();
        $airport2 = Airport::factory()->create();
        $group->airports()->sync([$airport1->id, $airport2->id]);

        $measure = FlowMeasure::factory()->withArrivalAirports(['EGKK', $group->id])->create();

        $this->assertEquals(
            'EGKK, Severn Clutch [' . $airport1->icao_code . ', ' . $airport2->icao_code . ']',
            $this->getField($measure)->value()
        );
    }
}
