<?php

namespace App\Discord\Message;

use App\Discord\Message\Embed\EmbedCollection;

/**
 * An interface for a discord message being sent to ECFMP's discord.
 */
interface EcfmpMessageInterface
{
    /**
     * The channel to send the message to.
     */
    public function channel(): string;

    /**
     * Returns the message content.
     */
    public function content(): string;

    /**
     * Returns an array of embeds
     */
    public function embeds(): EmbedCollection;
}
