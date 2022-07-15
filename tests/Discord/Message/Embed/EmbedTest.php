<?php

namespace Tests\Discord\Message\Embed;

use App\Discord\Message\Embed\AuthorInterface;
use App\Discord\Message\Embed\Colour;
use App\Discord\Message\Embed\DescriptionInterface;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\FieldInterface;
use App\Discord\Message\Embed\FooterInterface;
use App\Discord\Message\Embed\TitleInterface;
use Mockery;
use Tests\TestCase;

class EmbedTest extends TestCase
{
    public function testItHasATitle()
    {
        $mockTitle = Mockery::mock(TitleInterface::class);
        $mockTitle->shouldReceive('title')->once()->andReturn('Foo');

        $expected = [
            'title' => 'Foo',
        ];

        $this->assertEquals($expected, Embed::make()->withTitle($mockTitle)->toArray());
    }

    public function testItHasAColour()
    {
        $expected = [
            'color' => Colour::ACTIVATED->value,
        ];

        $this->assertEquals($expected, Embed::make()->withColour(Colour::ACTIVATED)->toArray());
    }

    public function testItHasAnAuthor()
    {
        $mockAuthor = Mockery::mock(AuthorInterface::class);
        $mockAuthor->shouldReceive('author')->once()->andReturn('Foo');
        $expected = [
            'author' => 'Foo',
        ];

        $this->assertEquals($expected, Embed::make()->withAuthor($mockAuthor)->toArray());
    }

    public function testItHasADescription()
    {
        $mockDescription = Mockery::mock(DescriptionInterface::class);
        $mockDescription->shouldReceive('description')->once()->andReturn('Foo');
        $expected = [
            'description' => 'Foo',
        ];

        $this->assertEquals($expected, Embed::make()->withDescription($mockDescription)->toArray());
    }

    public function testItHasFields()
    {
        $field1 = Mockery::mock(FieldInterface::class);
        $field1->shouldReceive('name')->once()->andReturn('Field1');
        $field1->shouldReceive('value')->once()->andReturn('Value1');
        $field1->shouldReceive('inline')->once()->andReturn(true);
        $field2 = Mockery::mock(FieldInterface::class);
        $field2->shouldReceive('name')->once()->andReturn('Field2');
        $field2->shouldReceive('value')->once()->andReturn('Value2');
        $field2->shouldReceive('inline')->once()->andReturn(false);

        $expected = [
            'fields' => [
                [
                    'name' => 'Field1',
                    'value' => 'Value1',
                    'inline' => true,
                ],
                [
                    'name' => 'Field2',
                    'value' => 'Value2',
                    'inline' => false,
                ],
            ],
        ];

        $this->assertEquals($expected, Embed::make()->withField($field1)->withField($field2)->toArray());
    }

    public function testItSkipsFieldsByCondition()
    {
        $field1 = Mockery::mock(FieldInterface::class);
        $field1->shouldReceive('name')->once()->andReturn('Field1');
        $field1->shouldReceive('value')->once()->andReturn('Value1');
        $field1->shouldReceive('inline')->once()->andReturn(true);
        $field2 = Mockery::mock(FieldInterface::class);

        $expected = [
            'fields' => [
                [
                    'name' => 'Field1',
                    'value' => 'Value1',
                    'inline' => true,
                ],
            ],
        ];

        $this->assertEquals($expected, Embed::make()->withField($field1)->withField($field2, false)->toArray());
    }

    public function testItHasFieldsByCollection()
    {
        $field1 = Mockery::mock(FieldInterface::class);
        $field1->shouldReceive('name')->once()->andReturn('Field1');
        $field1->shouldReceive('value')->once()->andReturn('Value1');
        $field1->shouldReceive('inline')->once()->andReturn(true);
        $field2 = Mockery::mock(FieldInterface::class);
        $field2->shouldReceive('name')->once()->andReturn('Field2');
        $field2->shouldReceive('value')->once()->andReturn('Value2');
        $field2->shouldReceive('inline')->once()->andReturn(false);

        $expected = [
            'fields' => [
                [
                    'name' => 'Field1',
                    'value' => 'Value1',
                    'inline' => true,
                ],
                [
                    'name' => 'Field2',
                    'value' => 'Value2',
                    'inline' => false,
                ],
            ],
        ];

        $this->assertEquals($expected, Embed::make()->withFields(collect([$field1, $field2]))->toArray());
    }

    public function testItHasAFooter()
    {
        $mockFooter = Mockery::mock(FooterInterface::class);
        $mockFooter->shouldReceive('footer')->once()->andReturn('Foo');
        $expected = [
            'footer' =>
                [
                    'text' => 'Foo',
                ],
        ];

        $this->assertEquals($expected, Embed::make()->withFooter($mockFooter)->toArray());
    }
}
