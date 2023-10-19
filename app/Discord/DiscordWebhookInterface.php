<?php

namespace App\Discord;

use App\Discord\Message\MessageInterface;

/**
 * To hide the details of how we go about doing Discord things...
 *
 * This class is the interface for interacting with Discord via webhooks.
 */
interface DiscordWebhookInterface
{
    public function sendMessage(MessageInterface $message): bool;
}
