<?php

namespace App\Discord\Message\Tag;

interface TagProviderInterface
{
    /**
     * Returns the raw, unedited string of the tag.
     */
    public function rawTagString(): string;
}
