<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ActivatedWebhookFilter implements FilterInterface
{
    use AppliesWebhookSpecificFilters;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return tap(
            $flowMeasure->activatedDiscordNotifications()
                ->where('discord_notification_flow_measure.notified_as', $flowMeasure->identifier),
            function (BelongsToMany $notifications) use ($webhook) {
                $this->filterQueryForWebhook($notifications, $webhook);
            }
        )->doesntExist();
    }
}
