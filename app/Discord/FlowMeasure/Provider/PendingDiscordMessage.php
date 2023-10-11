<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;

class PendingDiscordMessage implements PendingMessageInterface
{
    private readonly FlowMeasure $measure;
    private readonly DiscordNotificationType $type;
    private readonly ?WebhookInterface $webhook;
    private readonly NotificationReissuerInterface $resissue;

    public function __construct(
        FlowMeasure $measure,
        DiscordNotificationType $type,
        ?WebhookInterface $webhook,
        NotificationReissuerInterface $resissue
    ) {
        $this->measure = $measure;
        $this->type = $type;
        $this->webhook = $webhook;
        $this->resissue = $resissue;
    }

    public function flowMeasure(): FlowMeasure
    {
        return $this->measure;
    }

    public function type(): DiscordNotificationType
    {
        return $this->type;
    }

    public function webhook(): WebhookInterface
    {
        return $this->webhook;
    }

    public function reissue(): NotificationReissuerInterface
    {
        return $this->resissue;
    }
}
