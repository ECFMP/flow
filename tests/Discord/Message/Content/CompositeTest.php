<?php

namespace Tests\Discord\Message\Content;

use App\Discord\Message\Content\Composite;
use App\Discord\Message\Content\ContentInterface;
use Tests\TestCase;

class CompositeTest extends TestCase
{
    private function component1(): ContentInterface
    {
        return new class () implements ContentInterface {
            public function toString(): string
            {
                return "ohai1";
            }
        };
    }

    private function component2(): ContentInterface
    {
        return new class () implements ContentInterface {
            public function toString(): string
            {
                return "ohai2";
            }
        };
    }

    public function testItComposesComponents()
    {
        $this->assertEquals(
            'ohai1ohai2ohai1',
            Composite::make()
                ->addComponent($this->component1())
                ->addComponent($this->component2())
                ->addComponent($this->component1())
                ->toString()
        );
    }

    public function testItReturnsEmptyNoComponents()
    {
        $this->assertEquals(
            '',
            Composite::make()->toString()
        );
    }
}
