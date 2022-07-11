<?php

namespace App\Discord\Message\Embed;

class Field implements FieldInterface
{
    private readonly FieldProviderInterface $fieldProvider;
    private readonly bool $inline;

    private function __construct(FieldProviderInterface $fieldProvider, bool $inline)
    {
        $this->fieldProvider = $fieldProvider;
        $this->inline = $inline;
    }

    public static function make(FieldProviderInterface $fieldProvider): static
    {
        return new static($fieldProvider, false);
    }

    public static function makeInline(FieldProviderInterface $fieldProvider): static
    {
        return new static($fieldProvider, true);
    }

    public function name(): string
    {
        return $this->fieldProvider->name();
    }

    public function value(): string
    {
        return $this->fieldProvider->value();
    }

    public function inline(): bool
    {
        return $this->inline;
    }
}
