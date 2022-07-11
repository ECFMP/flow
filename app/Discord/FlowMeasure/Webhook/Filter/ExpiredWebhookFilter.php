<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;

class ExpiredWebhookFilter implements FilterInterface
{
    use ChecksForDiscordNotificationsToWebhook;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->existingNotificationDoesntExist(
            $flowMeasure->withdrawnAndExpiredDiscordNotifications(),
            $webhook
        );
    }
}
