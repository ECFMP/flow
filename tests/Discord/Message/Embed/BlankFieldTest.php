<?php

namespace Tests\Discord\Message\Embed;

use App\Discord\Message\Embed\BlankField;
use Tests\TestCase;

class BlankFieldTest extends TestCase
{
    public function testItReturnsADummyName()
    {
        $this->assertEquals(
            "\u{200b}",
            BlankField::make()->name()
        );
    }

    public function testItReturnsADummyValue()
    {
        $this->assertEquals(
            "\u{200b}",
            BlankField::make()->value()
        );
    }
}
