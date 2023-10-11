<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;

/**
 * Represents a pending message that needs to be sent out to Discord.
 */
interface PendingMessageInterface
{
    public function flowMeasure(): FlowMeasure;

    public function type(): DiscordNotificationType;

    public function reissue(): NotificationReissuerInterface;

    public function webhook(): ?WebhookInterface;
}
