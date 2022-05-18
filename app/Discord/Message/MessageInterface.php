<?php

namespace App\Discord\Message;

use App\Discord\Message\Embed\EmbedCollection;

/**
 * An interface for a discord message
 */
interface MessageInterface
{
    /**
     * Returns the message content.
     */
    public function content(): string;

    /**
     * Returns an array of embeds
     */
    public function embeds(): EmbedCollection;
}
