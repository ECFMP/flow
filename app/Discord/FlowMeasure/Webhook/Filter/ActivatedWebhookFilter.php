<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;

class ActivatedWebhookFilter implements FilterInterface
{
    use ChecksForDiscordNotificationsToWebhook;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->existingNotificationDoesntExist(
            $flowMeasure->activatedDiscordNotifications()
                ->where('division_discord_notification_flow_measure.notified_as', $flowMeasure->identifier),
            $webhook
        );
    }
}
