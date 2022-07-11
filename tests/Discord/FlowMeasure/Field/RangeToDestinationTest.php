<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\RangeToDestination;
use Tests\TestCase;

class RangeToDestinationTest extends TestCase
{
    private function getField(): RangeToDestination
    {
        return new RangeToDestination(
            [
                'type' => 'range_to_destination',
                'value' => '250',
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Range To Destination (NM)',
            $this->getField()->name()
        );
    }

    public function testItHasARange()
    {
        $this->assertEquals(
            '250',
            $this->getField()->value()
        );
    }
}
