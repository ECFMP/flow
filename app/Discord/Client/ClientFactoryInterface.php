<?php

namespace App\Discord\Client;

use Ecfmp_discord\DiscordClient;

interface ClientFactoryInterface
{
    public function create(): DiscordClient;
}
