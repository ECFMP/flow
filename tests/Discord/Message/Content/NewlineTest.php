<?php

namespace Tests\Discord\Message\Content;

use App\Discord\Message\Content\Newline;
use Tests\TestCase;

class NewlineTest extends TestCase
{
    public function testItDefaultsToOneNewline()
    {
        $this->assertEquals(
            "\n",
            Newline::make()->toString()
        );
    }

    public function testItComposesManyNewlines()
    {
        $this->assertEquals(
            "\n\n\n\n\n",
            Newline::make(5)->toString()
        );
    }
}
