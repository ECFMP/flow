<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Restriction;
use App\Enums\FlowMeasureType;
use App\Models\FlowMeasure;
use Tests\TestCase;

class RestrictionTest extends TestCase
{
    private function getField(FlowMeasure $measure): Restriction
    {
        return new Restriction($measure);
    }

    public function testItCanBeAMandatoryRoute()
    {
        $measure = FlowMeasure::factory()
            ->withMandatoryRoute()
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Mandatory route',
            $field->name()
        );

        $this->assertEquals(
            'LOGAN, UL612 LAKEY DCT NUGRA',
            $field->value()
        );
    }

    public function testItCanBeAMinimumDepartureIntervalSecondsOnly()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 50)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Minimum Departure Interval [MDI]',
            $field->name()
        );

        $this->assertEquals(
            '50 Seconds',
            $field->value()
        );
    }

    public function testItCanBeAMinimumDepartureIntervalMinutesOnly()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 120)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Minimum Departure Interval [MDI]',
            $field->name()
        );

        $this->assertEquals(
            '2 Minutes',
            $field->value()
        );
    }

    public function testItCanBeAMinimumDepartureIntervalMinutesAndSeconds()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 140)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Minimum Departure Interval [MDI]',
            $field->name()
        );

        $this->assertEquals(
            '2 Minutes 20 Seconds',
            $field->value()
        );
    }

    public function testItCanBeAnAverageDepartureIntervalSecondsOnly()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL, 50)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Average Departure Interval [ADI]',
            $field->name()
        );

        $this->assertEquals(
            '50 Seconds',
            $field->value()
        );
    }

    public function testItCanBeAnAverageDepartureIntervalMinutesOnly()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL, 120)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Average Departure Interval [ADI]',
            $field->name()
        );

        $this->assertEquals(
            '2 Minutes',
            $field->value()
        );
    }

    public function testItCanBeAnAverageDepartureIntervalMinutesAndSeconds()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL, 140)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Average Departure Interval [ADI]',
            $field->name()
        );

        $this->assertEquals(
            '2 Minutes 20 Seconds',
            $field->value()
        );
    }

    public function testItCanBePerHour()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::PER_HOUR, 12)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Per hour',
            $field->name()
        );

        $this->assertEquals(
            '12',
            $field->value()
        );
    }

    public function testItCanBeMilesInTrail()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MILES_IN_TRAIL, 7)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Miles In Trail [MIT]',
            $field->name()
        );

        $this->assertEquals(
            '7 NM',
            $field->value()
        );
    }

    public function testItCanBeMaxAirspeed()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MAX_IAS, 225)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Max IAS',
            $field->name()
        );

        $this->assertEquals(
            '225 kts',
            $field->value()
        );
    }

    public function testItCanBeIasReduction()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::IAS_REDUCTION, 35)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'IAS reduction',
            $field->name()
        );

        $this->assertEquals(
            '35 kts',
            $field->value()
        );
    }

    public function testItCanBeMaxMachBelow1()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MAX_MACH, 84)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Max Mach',
            $field->name()
        );

        $this->assertEquals(
            '0.84',
            $field->value()
        );
    }

    public function testItCanBeMaxMachGreaterThan1()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MAX_MACH, 105)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Max Mach',
            $field->name()
        );

        $this->assertEquals(
            '1.05',
            $field->value()
        );
    }

    public function testItCanBeMachReductionBelow1()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MACH_REDUCTION, 23)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Mach reduction',
            $field->name()
        );

        $this->assertEquals(
            '0.23',
            $field->value()
        );
    }

    public function testItCanBeMachReductionGreaterThan1()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::MACH_REDUCTION, 123)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Mach reduction',
            $field->name()
        );

        $this->assertEquals(
            '1.23',
            $field->value()
        );
    }

    public function testItCanBeProhibit()
    {
        $measure = FlowMeasure::factory()
            ->withMeasure(FlowMeasureType::PROHIBIT, null)
            ->make();

        $field = $this->getField($measure);

        $this->assertEquals(
            'Prohibit',
            $field->name()
        );

        $this->assertEquals(
            'N/A',
            $field->value()
        );
    }
}
