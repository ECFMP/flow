<?php

namespace App\Discord\Message\Embed;

class BlankField implements FieldProviderInterface
{
    const PLACEHOLDER = "\u{200b}";

    private function __construct()
    {}

    public static function make(): static
    {
        return new static();
    }

    public function name(): string
    {
        return self::PLACEHOLDER;
    }

    public function value(): string
    {
        return self::PLACEHOLDER;
    }
}
