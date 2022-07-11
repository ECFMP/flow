<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\ViaWaypoint;
use Tests\TestCase;

class ViaWaypointTest extends TestCase
{
    private function getField(): ViaWaypoint
    {
        return new ViaWaypoint(
            [
                'type' => 'waypoint',
                'value' => ['XAMAB', 'WOTAN L9 CPT'],
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Via Waypoint',
            $this->getField()->name()
        );
    }

    public function testItHasALevel()
    {
        $this->assertEquals(
            'XAMAB, WOTAN L9 CPT',
            $this->getField()->value()
        );
    }
}
