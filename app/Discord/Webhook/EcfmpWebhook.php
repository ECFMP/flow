<?php

namespace App\Discord\Webhook;

class EcfmpWebhook implements WebhookInterface
{
    public function url(): string
    {
        return config('discord.webhook_url');
    }
}
