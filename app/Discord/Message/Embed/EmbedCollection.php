<?php

namespace App\Discord\Message\Embed;

use Illuminate\Support\Collection;

class EmbedCollection
{
    private Collection $embeds;

    public function __construct()
    {
        $this->embeds = collect();
    }

    public static function make(): static
    {
        return new static();
    }

    public function add(EmbedInterface $embed): static
    {
        $this->embeds->add($embed);

        return $this;
    }

    public function toArray(): array
    {
        return $this->embeds->map(fn (EmbedInterface $embed) => $embed->toArray())->toArray();
    }
}
