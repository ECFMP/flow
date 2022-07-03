<?php

namespace App\Discord\FlowMeasure\Webhook\Filter;

use App\Discord\Webhook\WebhookInterface;
use App\Models\FlowMeasure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExpiredWebhookFilter implements FilterInterface
{
    use AppliesWebhookSpecificFilters;

    public function shouldUseWebhook(FlowMeasure $flowMeasure, WebhookInterface $webhook): bool
    {
        return tap(
            $flowMeasure->withdrawnAndExpiredDiscordNotifications(),
            function (BelongsToMany $notifications) use ($webhook) {
                $this->filterQueryForWebhook($notifications, $webhook);
            }
        )->doesntExist();
    }
}
