<?php

namespace App\Discord\Message\Associator;

use App\Models\DivisionDiscordNotification;

interface AssociatorInterface
{
    public function associate(DivisionDiscordNotification $notification): void;
}
