<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait AppliesWebhookSpecificFilters
{
    private function filterQueryForWebhook(BelongsToMany $query, WebhookInterface $webhook): void
    {
        is_null($webhook->id())
            ? $query->isEcfmp()
            : $query->isDivision();
    }
}
