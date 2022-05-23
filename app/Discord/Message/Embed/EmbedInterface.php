<?php

namespace App\Discord\Message\Embed;

interface EmbedInterface
{
    /**
     * Converts the embed to array.
     */
    public function toArray(): array;
}
