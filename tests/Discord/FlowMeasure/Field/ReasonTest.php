<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Reason;
use App\Models\FlowMeasure;
use Tests\TestCase;

class ReasonTest extends TestCase
{
    private function getField(FlowMeasure $measure): Reason
    {
        return new Reason($measure);
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Reason',
            $this->getField(FlowMeasure::factory()->make())->name()
        );
    }

    public function testItHasAReason()
    {
        $measure = FlowMeasure::factory()->make();

        $this->assertEquals(
            $measure->reason,
            $this->getField($measure)->value()
        );
    }

    public function testItHasNoAReason()
    {
        $measure = FlowMeasure::factory()->make();
        $measure->reason = null;

        $this->assertEquals(
            'No reason given',
            $this->getField($measure)->value()
        );
    }
}
