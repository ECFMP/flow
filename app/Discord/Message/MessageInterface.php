<?php

namespace App\Discord\Message;

use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Webhook\WebhookInterface;

/**
 * An interface for a discord message
 */
interface MessageInterface
{
    /**
     * Where are we sending this message to?
     */
    public function destination(): WebhookInterface;

    /**
     * Returns the message content.
     */
    public function content(): string;

    /**
     * Returns an array of embeds
     */
    public function embeds(): EmbedCollection;
}
