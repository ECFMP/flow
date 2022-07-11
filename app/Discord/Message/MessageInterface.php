<?php

namespace App\Discord\Message;

use App\Discord\Message\Associator\AssociatorInterface;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Logger\LoggerInterface;
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


    /**
     * Associates the discord notification with.
     */
    public function associator(): AssociatorInterface;

    /**
     * Logs the discord message
     */
    public function logger(): LoggerInterface;
}
