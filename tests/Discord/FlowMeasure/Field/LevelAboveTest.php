<?php

namespace Tests\Discord\FlowMeasure\Field;

use App\Discord\FlowMeasure\Field\Filters\LevelAbove;
use Tests\TestCase;

class LevelAboveTest extends TestCase
{
    private function getField(): LevelAbove
    {
        return new LevelAbove(
            [
                'type' => 'level_above',
                'value' => [555, 556],
            ]
        );
    }

    public function testItHasAName()
    {
        $this->assertEquals(
            'Level at or Above',
            $this->getField()->name()
        );
    }

    public function testItHasALevelAbove()
    {
        $this->assertEquals(
            '555, 556',
            $this->getField()->value()
        );
    }
}
