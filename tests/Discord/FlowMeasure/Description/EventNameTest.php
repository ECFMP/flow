<?php

namespace Tests\Discord\FlowMeasure\Description;

use App\Discord\FlowMeasure\Description\EventName;
use App\Models\FlowMeasure;
use Tests\TestCase;

class EventNameTest extends TestCase
{
    public function testItDescribesTheEvent()
    {
        $measure = FlowMeasure::factory()->withEvent()->create();

        $this->assertEquals(
            $measure->event->name,
            (new EventName($measure))->description()
        );
    }

    public function testItReturnsBlankIfThereIsNoEvent()
    {
        $measure = FlowMeasure::factory()->create();

        $this->assertEquals(
            '',
            (new EventName($measure))->description()
        );
    }
}
