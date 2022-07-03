<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class NotifiedWebhookFilter implements FilterInterface
{
    use AppliesWebhookSpecificFilters;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->notYetNotified($flowMeasure, $webhook) && $this->notYetActivated($flowMeasure, $webhook);
    }

    /**
     * We notify again, if it was previously notified as a different flow measure.
     */
    private function notYetNotified(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return tap(
            $flowMeasure->notifiedDiscordNotifications()
                ->where('discord_notification_flow_measure.notified_as', $flowMeasure->identifier),
            function (BelongsToMany $notifications) use ($webhook) {
                $this->filterQueryForWebhook($notifications, $webhook);
            }
        )->doesntExist();
    }

    private function notYetActivated(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return tap(
            $flowMeasure->activatedDiscordNotifications(),
            function (BelongsToMany $notifications) use ($webhook) {
                $this->filterQueryForWebhook($notifications, $webhook);
            }
        )->doesntExist();
    }
}
