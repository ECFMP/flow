<?php

namespace App\Discord\Message\Logger;

use App\Models\DiscordNotification;

interface LoggerInterface
{
    public function log(DiscordNotification $notification): void;
}
