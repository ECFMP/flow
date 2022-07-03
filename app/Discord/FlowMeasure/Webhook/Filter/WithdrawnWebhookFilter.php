<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WithdrawnWebhookFilter implements FilterInterface
{
    use AppliesWebhookSpecificFilters;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return $this->hasBeenActivatedOrNotified($flowMeasure, $webhook) &&
            $this->hasNotBeenWithdrawnOrExpired($flowMeasure, $webhook);
    }

    private function hasBeenActivatedOrNotified(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return tap(
            $flowMeasure->activatedAndNotifiedNotifications(),
            function (BelongsToMany $notifications) use ($webhook) {
                $this->filterQueryForWebhook($notifications, $webhook);
            }
        )->exists();
    }

    private function hasNotBeenWithdrawnOrExpired(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return tap(
            $flowMeasure->withdrawnAndExpiredDiscordNotifications(),
            function (BelongsToMany $notifications) use ($webhook) {
                $this->filterQueryForWebhook($notifications, $webhook);
            }
        )->doesntExist();
    }
}
