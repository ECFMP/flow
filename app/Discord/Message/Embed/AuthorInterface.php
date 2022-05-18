<?php

namespace App\Discord\Message\Embed;

interface AuthorInterface
{
    /**
     * The author of the embed.
     */
    public function author(): string;
}
