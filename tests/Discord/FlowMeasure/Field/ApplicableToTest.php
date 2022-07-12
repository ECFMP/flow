<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\ApplicableTo;
use App\Models\FlowMeasure;
use Tests\TestCase;

class ApplicableToTest extends TestCase
{
    private readonly FlowMeasure $flowMeasure;
    private readonly ApplicableTo $applicableTo;

    public function setUp(): void
    {
        parent::setUp();
        $this->flowMeasure = FlowMeasure::factory()->create();
        $this->applicableTo = new ApplicableTo($this->flowMeasure);
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Applicable To FIR(s)',
            $this->applicableTo->name()
        );
    }

    public function testItIsEmptyWithNoNotifiedFlightInformationRegions()
    {
        $this->assertEquals(
            '--',
            $this->applicableTo->value()
        );
    }

    public function testItHasAValueWithOneNotifiedFlightInformationRegion()
    {
        $this->flowMeasure->notifiedFlightInformationRegions()->create(
            [
                'identifier' => 'EGTT',
                'name' => 'London',
            ]
        );

        $this->assertEquals(
            'EGTT',
            $this->applicableTo->value()
        );
    }

    public function testItHasAValueWithMultipleNotifiedFlightInformationRegions()
    {
        $this->flowMeasure->notifiedFlightInformationRegions()->create(
            [
                'identifier' => 'EGTT',
                'name' => 'London',
            ]
        );

        $this->flowMeasure->notifiedFlightInformationRegions()->create(
            [
                'identifier' => 'EGPX',
                'name' => 'Scottish',
            ]
        );

        $this->assertEquals(
            'EGTT, EGPX',
            $this->applicableTo->value()
        );
    }
}
