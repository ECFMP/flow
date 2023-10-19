<?php

namespace App\Discord\Message\Logger;

use App\Models\DivisionDiscordNotification;

interface LoggerInterface
{
    public function log(DivisionDiscordNotification $notification): void;
}
