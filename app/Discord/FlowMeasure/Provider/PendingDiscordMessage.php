<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;

class PendingDiscordMessage implements PendingMessageInterface
{
    private readonly FlowMeasure $measure;
    private readonly DiscordNotificationType $type;
    private readonly WebhookInterface $webhook;
    private readonly bool $resissue;

    public function __construct(
        FlowMeasure $measure,
        DiscordNotificationType $type,
        WebhookInterface $webhook,
        bool $resissue
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

    public function reissue(): bool
    {
        return $this->resissue;
    }
}
