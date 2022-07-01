<?php

namespace Tests\Discord\Message\Tag;

use App\Discord\Message\Tag\Tag;
use App\Discord\Message\Tag\TagProviderInterface;
use Mockery;
use Tests\TestCase;

class TagTest extends TestCase
{
    private readonly TagProviderInterface $mockTagProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockTagProvider = Mockery::mock(TagProviderInterface::class);
    }

    public function testItFormatsTagInCorrectFormat()
    {
        $this->mockTagProvider->expects('rawTagString')->once()->andReturn('@1234');

        $this->assertEquals(
            '<@1234>',
            (string) (new Tag($this->mockTagProvider))
        );
    }

    public function testItFormatsTagWithMissingStartSymbol()
    {
        $this->mockTagProvider->expects('rawTagString')->once()->andReturn('1234');

        $this->assertEquals(
            '<@1234>',
            (string) (new Tag($this->mockTagProvider))
        );
    }
}
