<?php

namespace App\Discord\Message\Embed;

interface DescriptionInterface
{
    /**
     * The description of the embed.
     */
    public function description(): string;
}
