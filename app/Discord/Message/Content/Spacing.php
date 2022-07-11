<?php

namespace App\Discord\Message\Content;

use Str;

class Spacing implements ContentInterface
{
    private readonly int $count;

    private function __construct(int $count)
    {
        $this->count = $count;
    }

    public static function make(int $count = 1): static
    {
        return new static($count);
    }

    public function toString(): string
    {
        return Str::padLeft('', $this->count, ' ');
    }
}
