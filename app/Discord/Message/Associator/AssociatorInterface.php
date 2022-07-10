<?php

namespace App\Discord\Message\Associator;

use App\Models\DiscordNotification;

interface AssociatorInterface
{
    public function associate(DiscordNotification $notification): void;
}
