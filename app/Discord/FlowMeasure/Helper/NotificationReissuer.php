<?php

namespace App\Discord\FlowMeasure\Helper;

use App\Enums\DiscordNotificationType;
use App\Models\DiscordNotification;
use App\Models\FlowMeasure;

class NotificationReissuer implements NotificationReissuerInterface
{
    private readonly FlowMeasure $measure;
    private readonly DiscordNotificationType $type;

    public function __construct(FlowMeasure $measure, DiscordNotificationType $type)
    {
        $this->measure = $measure;
        $this->type = $type;
    }

    public function isReissuedNotification(): bool
    {
        if (
            $this->type !== DiscordNotificationType::FLOW_MEASURE_ACTIVATED &&
            $this->type !== DiscordNotificationType::FLOW_MEASURE_NOTIFIED
        ) {
            return false;
        }

        $notificationsOfType = $this->measure->activatedAndNotifiedNotifications()->get();

        return $notificationsOfType->filter(
                fn(DiscordNotification $notification
                ) => $notification->pivot->notified_as !== $this->measure->identifier
            )->isNotEmpty() && $notificationsOfType->filter(
                fn(DiscordNotification $notification
                ) => $notification->pivot->notified_as === $this->measure->identifier
            )->isEmpty();
    }
}
