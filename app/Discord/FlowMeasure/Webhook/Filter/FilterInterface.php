<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;

interface FilterInterface
{
    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool;
}
