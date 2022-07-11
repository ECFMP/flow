<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;

interface PendingMessageInterface
{
    public function flowMeasure(): FlowMeasure;

    public function type(): DiscordNotificationType;

    public function webhook(): WebhookInterface;

    public function reissue(): NotificationReissuerInterface;
}
