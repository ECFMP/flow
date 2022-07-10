<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;

class NotifiedWebhookFilter implements FilterInterface
{
    use ChecksForDiscordNotificationsToWebhook;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->notYetNotified($flowMeasure, $webhook) && $this->notYetActivated($flowMeasure, $webhook);
    }

    /**
     * We notify again, if it was previously notified as a different flow measure.
     */
    private function notYetNotified(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->existingNotificationDoesntExist(
            $flowMeasure->notifiedDiscordNotifications()
                ->where('discord_notification_flow_measure.notified_as', $flowMeasure->identifier),
            $webhook
        );
    }

    private function notYetActivated(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->existingNotificationDoesntExist(
            $flowMeasure->activatedDiscordNotifications(),
            $webhook
        );
    }
}
