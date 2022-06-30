<?php

namespace App\Discord\Webhook;

interface WebhookInterface
{
    /**
     * Returns the URL for the webhook
     */
    public function url(): string;
}
