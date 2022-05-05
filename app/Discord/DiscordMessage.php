<?php

namespace App\Discord;

class DiscordMessage implements MessageInterface
{
    public function content(): string
    {
        return 'A flow measure got created... aren\'t you a lucky person.';
    }
}
