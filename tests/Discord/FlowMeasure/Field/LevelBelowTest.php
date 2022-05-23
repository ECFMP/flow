<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\LevelBelow;
use Tests\TestCase;

class LevelBelowTest extends TestCase
{
    private function getField(): LevelBelow
    {
        return new LevelBelow(
            [
                'type' => 'level_below',
                'value' => 555,
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Level at or Below',
            $this->getField()->name()
        );
    }

    public function testItHasALevelBelow()
    {
        $this->assertEquals(
            '555',
            $this->getField()->value()
        );
    }
}
