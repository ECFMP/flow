<?php

namespace App\Discord\FlowMeasure\Provider;

use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;

class PendingEcfmpMessage implements PendingMessageInterface
{
    private readonly FlowMeasure $flowMeasure;

    private readonly DiscordNotificationType $type;

    private readonly NotificationReissuerInterface $reissue;

    public function __construct(FlowMeasure $flowMeasure, DiscordNotificationType $type, NotificationReissuerInterface $reissue)
    {
        $this->flowMeasure = $flowMeasure;
        $this->type = $type;
        $this->reissue = $reissue;
    }

    public function flowMeasure(): FlowMeasure
    {
        return $this->flowMeasure;
    }

    public function type(): DiscordNotificationType
    {
        return $this->type;
    }

    public function reissue(): NotificationReissuerInterface
    {
        return $this->reissue;
    }

    public function isEcfmp(): bool
    {
        return true;
    }
}
