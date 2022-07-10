<?php

namespace App\Discord\Webhook;

interface WebhookInterface
{
    /**
     * The id of the webhook. May be null if its the system webhook.
     */
    public function id(): ?int;

    /**
     * Returns the URL for the webhook
     */
    public function url(): string;

    /**
     * A description of the webhook.
     */
    public function description(): string;
}
