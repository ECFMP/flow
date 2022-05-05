<?php

namespace App\Discord;

/**
 * An interface for a discord message
 */
interface MessageInterface
{
    /**
     * Returns the message content.
     */
    public function content(): string;
}
