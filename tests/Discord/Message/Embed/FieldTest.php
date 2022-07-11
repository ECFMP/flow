<?php

namespace Tests\Discord\Message\Embed;

use App\Discord\Message\Embed\Field;
use App\Discord\Message\Embed\FieldProviderInterface;
use Mockery;
use Tests\TestCase;

class FieldTest extends TestCase
{
    public function testItMakes()
    {
        $mockProvider = Mockery::mock(FieldProviderInterface::class);
        $mockProvider->shouldReceive('name')->once()->andReturn('TestName');
        $mockProvider->shouldReceive('value')->once()->andReturn('TestValue');
        $field = Field::make($mockProvider);

        $this->assertEquals('TestName', $field->name());
        $this->assertEquals('TestValue', $field->value());
        $this->assertFalse($field->inline());
    }

    public function testItMakesInline()
    {
        $mockProvider = Mockery::mock(FieldProviderInterface::class);
        $mockProvider->shouldReceive('name')->once()->andReturn('TestName');
        $mockProvider->shouldReceive('value')->once()->andReturn('TestValue');
        $field = Field::makeInline($mockProvider);

        $this->assertEquals('TestName', $field->name());
        $this->assertEquals('TestValue', $field->value());
        $this->assertTrue($field->inline());
    }
}
