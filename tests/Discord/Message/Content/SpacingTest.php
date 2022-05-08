<?php

namespace Tests\Discord\Message\Content;

use App\Discord\Message\Content\Spacing;
use Tests\TestCase;

class SpacingTest extends TestCase
{
    public function testItDefaultsToOneSpace()
    {
        $this->assertEquals(
            ' ',
            Spacing::make()->toString()
        );
    }

    public function testItPadsMultipleSpaces()
    {
        $this->assertEquals(
            '          ',
            Spacing::make(10)->toString()
        );
    }
}
