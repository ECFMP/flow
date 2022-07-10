<?php

namespace App\Discord\FlowMeasure\Embed;

use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Enums\DiscordNotificationType;

class FlowMeasureEmbedFactory
{
    public function make(PendingMessageInterface $pendingMessage): FlowMeasureEmbedInterface
    {
        return match ($pendingMessage->type()) {
            DiscordNotificationType::FLOW_MEASURE_ACTIVATED => new ActivatedEmbeds($pendingMessage),
            DiscordNotificationType::FLOW_MEASURE_NOTIFIED => new NotifiedEmbeds($pendingMessage),
            DiscordNotificationType::FLOW_MEASURE_WITHDRAWN => new WithdrawnEmbeds($pendingMessage),
            DiscordNotificationType::FLOW_MEASURE_EXPIRED => new ExpiredEmbeds($pendingMessage)
        };
    }
}
