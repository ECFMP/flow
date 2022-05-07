<?php

namespace App\Discord\Message\Content;

use Illuminate\Support\Arr;

class Composite implements ContentInterface
{
    private array $components;

    private function __construct()
    {
        $this->components = [];
    }

    public static function make(): static
    {
        return new static();
    }

    public function addComponent(ContentInterface $content): static
    {
        $this->components[] = $content;
        return $this;
    }

    public function toString(): string
    {
        return array_reduce(
            $this->components,
            fn (string $carry, ContentInterface $content) => $carry . $content->toString(),
            ''
        );
    }
}
