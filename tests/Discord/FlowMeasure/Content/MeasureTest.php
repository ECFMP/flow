<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\Measure;
use App\Enums\FlowMeasureType;
use App\Models\FlowMeasure;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MeasureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
    }

    public function getContent(FlowMeasure $flowMeasure): string
    {
        return (new Measure($flowMeasure))->toString();
    }

    public function testItReturnsProhibit()
    {
        $this->assertEquals(
            'Prohibit',
            $this->getContent(FlowMeasure::factory()->withMeasure(FlowMeasureType::PROHIBIT, null)->create())
        );
    }

    public function testItReturnsMandatoryRoute()
    {
        $this->assertEquals(
            'Mandatory route: LOGAN, UL612 LAKEY DCT NUGRA',
            $this->getContent(FlowMeasure::factory()->withMandatoryRoute()->create())
        );
    }

    public function testItReturnsSecondsOnlyMinimumDepartureInterval()
    {
        $this->assertEquals(
            'Minimum Departure Interval [MDI]: 55 SECS',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 55)->create()
            )
        );
    }

    public function testItReturnsMinutesOnlyMinimumDepartureInterval()
    {
        $this->assertEquals(
            'Minimum Departure Interval [MDI]: 2 MINS',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 120)->create()
            )
        );
    }

    public function testItReturnsMinutesAndSecondsMinimumDepartureInterval()
    {
        $this->assertEquals(
            'Minimum Departure Interval [MDI]: 2 MINS 30 SECS',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 150)->create()
            )
        );
    }

    public function testItReturnsSecondsOnlyAverageDepartureInterval()
    {
        $this->assertEquals(
            'Average Departure Interval [ADI]: 55 SECS',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL, 55)->create()
            )
        );
    }

    public function testItReturnsMinutesOnlyAverageDepartureInterval()
    {
        $this->assertEquals(
            'Average Departure Interval [ADI]: 2 MINS',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL, 120)->create()
            )
        );
    }

    public function testItReturnsMinutesAndSecondsAverageDepartureInterval()
    {
        $this->assertEquals(
            'Average Departure Interval [ADI]: 2 MINS 30 SECS',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::AVERAGE_DEPARTURE_INTERVAL, 150)->create()
            )
        );
    }

    public function testItReturnsPerHour()
    {
        $this->assertEquals(
            'Per hour: 15',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::PER_HOUR, 15)->create()
            )
        );
    }

    public function testItReturnsMilesInTrail()
    {
        $this->assertEquals(
            'Miles In Trail [MIT]: 5 NM',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MILES_IN_TRAIL, 5)->create()
            )
        );
    }

    public function testItReturnsMaxIas()
    {
        $this->assertEquals(
            'Max IAS: 275 kts',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MAX_IAS, 275)->create()
            )
        );
    }

    public function testItReturnsIasReduction()
    {
        $this->assertEquals(
            'IAS reduction: 25 kts',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::IAS_REDUCTION, 25)->create()
            )
        );
    }

    public function testItReturnsMaxMachBelowOne()
    {
        $this->assertEquals(
            'Max Mach: 0.83',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MAX_MACH, 83)->create()
            )
        );
    }

    public function testItReturnsMaxMachGreaterThanOne()
    {
        $this->assertEquals(
            'Max Mach: 1.05',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MAX_MACH, 105)->create()
            )
        );
    }

    public function testItReturnsMachReductionBelowOne()
    {
        $this->assertEquals(
            'Mach reduction: 0.05',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MACH_REDUCTION, 5)->create()
            )
        );
    }

    public function testItReturnsMachReductionGreaterThanOne()
    {
        $this->assertEquals(
            'Mach reduction: 1.05',
            $this->getContent(
                FlowMeasure::factory()->withMeasure(FlowMeasureType::MACH_REDUCTION, 105)->create()
            )
        );
    }
}
