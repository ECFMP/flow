<?php

namespace App\Discord;

use App\Discord\Exception\DiscordServiceException;
use App\Discord\Message\MessageInterface;

interface DiscordServiceInterface
{
    /**
     * Returns the remote message id, or throws an exception if the message could not be sent.
     *
     * @throws DiscordServiceException
     */
    public function sendMessage(string $clientRequestId, MessageInterface $message): string;
}
