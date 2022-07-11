<?php

namespace App\Discord\Message\Content;

use Illuminate\Support\Str;

class Newline implements ContentInterface
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
        return Str::padLeft('', $this->count, "\n");
    }
}
