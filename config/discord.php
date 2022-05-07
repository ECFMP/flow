<?php

return [
    'enabled' => env('DISCORD_NOTIFICATIONS_ENABLE', false),
    'webhook_url' => env('DISCORD_WEBHOOK_URL', ''),
    'token' => env('DISCORD_AUTH_TOKEN', '')
];
