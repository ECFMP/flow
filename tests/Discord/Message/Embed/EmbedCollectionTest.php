<?php


namespace Tests\Discord\Message\Embed;


use App\Discord\Message\Embed\AuthorInterface;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use Mockery;
use Tests\TestCase;

class EmbedCollectionTest extends TestCase
{
    public function testItMapsEmbeds()
    {
        $mockAuthor1 = Mockery::mock(AuthorInterface::class);
        $mockAuthor1->shouldReceive('author')->once()->andReturn('Foo');
        $mockAuthor2 = Mockery::mock(AuthorInterface::class);
        $mockAuthor2->shouldReceive('author')->once()->andReturn('Bar');

        $expected = [
            [
                'author' => 'Foo',
            ],
            [
                'author' => 'Bar',
            ],
        ];

        $this->assertEquals(
            $expected,
            (new EmbedCollection())
                ->add(Embed::make()->withAuthor($mockAuthor1))
                ->add(Embed::make()->withAuthor($mockAuthor2))
                ->toArray()
        );
    }
}
