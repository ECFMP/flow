<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\Webhook\WebhookInterface;

interface PendingWebhookMessageInterface extends PendingMessageInterface
{
    public function webhook(): WebhookInterface;
}
