<?php

namespace Tests\Discord\Message\Content;

use App\Discord\Message\Content\ContentInterface;
use App\Discord\Message\Content\SnippetBlock;
use Tests\TestCase;

class SnippetBlockTest extends TestCase
{
    private function content(): ContentInterface
    {
        return new class implements ContentInterface {
            public function toString(): string
            {
                return 'abc';
            }
        };
    }

    public function testItCreatesASnippet()
    {
        $this->assertEquals(
            "```\nabc\n```",
            (new SnippetBlock($this->content()))->toString()
        );
    }
}
