<?php

namespace App\Discord\Message\Content;

interface ContentInterface
{
    /**
     * Converts the content to string.
     *
     * @return string
     */
    public function toString(): string;
}
