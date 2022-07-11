<?php

namespace App\Discord\Message\Tag;

interface TagInterface
{
    /**
     * Returns the discord tag, as a string.
     */
    public function __toString(): string;
}
