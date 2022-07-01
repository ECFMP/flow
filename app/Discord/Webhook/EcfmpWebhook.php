<?php

namespace App\Discord\Webhook;

class EcfmpWebhook implements WebhookInterface
{
    public function id(): ?int
    {
        return null;
    }

    public function url(): string
    {
        return config('discord.webhook_url');
    }

    public function description(): string
    {
        return 'ECFMP';
    }
}
