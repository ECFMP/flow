<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ChecksForDiscordNotificationsToWebhook
{
    private function filterQueryForWebhook(BelongsToMany $query, WebhookInterface $webhook): BelongsToMany
    {
        return is_null($webhook->id())
            ? $query->isEcfmp()
            : $query->isDivision();
    }

    private function existingNotifications(BelongsToMany $query, WebhookInterface $webhook): BelongsToMany
    {
        return $this->filterQueryForWebhook($query, $webhook);
    }

    private function existingNotificationDoesntExist(BelongsToMany $query, WebhookInterface $webhook): bool
    {
        return $this->existingNotifications($query, $webhook)
            ->doesntExist();
    }

    private function existingNotificationExists(BelongsToMany $query, WebhookInterface $webhook): bool
    {
        return $this->existingNotifications($query, $webhook)
            ->exists();
    }
}
