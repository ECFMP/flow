<?php

namespace App\Discord;

use App\Discord\Message\MessageInterface;
use App\Discord\Webhook\WebhookInterface;

/**
 * To hide the details of how we go about doing Discord things...
 *
 * This class is the interface for interacting with Discord.
 */
interface DiscordInterface
{
    public function sendMessage(WebhookInterface $webhook, MessageInterface $message): bool;
}
