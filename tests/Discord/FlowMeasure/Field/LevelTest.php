<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\Level;
use Tests\TestCase;

class LevelTest extends TestCase
{
    private function getField(): Level
    {
        return new Level(
            [
                'type' => 'level',
                'value' => 555,
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'At Levels',
            $this->getField()->name()
        );
    }

    public function testItHasALevel()
    {
        $this->assertEquals(
            '555',
            $this->getField()->value()
        );
    }
}
