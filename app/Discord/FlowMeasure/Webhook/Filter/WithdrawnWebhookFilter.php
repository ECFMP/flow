<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;

class WithdrawnWebhookFilter implements FilterInterface
{
    use ChecksForDiscordNotificationsToWebhook;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->hasBeenActivatedOrNotified($flowMeasure, $webhook) &&
            $this->hasNotBeenWithdrawnOrExpired($flowMeasure, $webhook);
    }

    private function hasBeenActivatedOrNotified(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->existingNotificationExists(
            $flowMeasure->activatedAndNotifiedNotifications(),
            $webhook
        );
    }

    private function hasNotBeenWithdrawnOrExpired(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->existingNotificationDoesntExist(
            $flowMeasure->withdrawnAndExpiredDiscordNotifications(),
            $webhook
        );
    }
}
