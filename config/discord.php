<?php

return [
    'username' => env('DISCORD_USERNAME', 'FlowBot'),
    'avatar_url' => env('DISCORD_AVATAR_URL', sprintf('%s/images/logo.png', env('APP_URL'))),
    'enabled' => env('DISCORD_NOTIFICATIONS_ENABLE', false),
    'webhook_url' => env('DISCORD_WEBHOOK_URL', ''),
    'token' => env('DISCORD_AUTH_TOKEN', ''),
    'service_host' => env('DISCORD_BOT_SERVICE_URL', 'localhost'),
    'service_token' => env('DISCORD_BOT_JWT', ''),
    'ecfmp_channel_id' => env('DISCORD_ECFMP_CHANNEL_ID', ''),
    'client_request_app_id' => env('DISCORD_CLIENT_REQUEST_APP_ID', ''),
];
