<?php

namespace App\Discord\FlowMeasure\Helper;

use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType;
use App\Models\DivisionDiscordNotification;
use App\Models\FlowMeasure;

class NotificationReissuer implements NotificationReissuerInterface
{
    private readonly FlowMeasure $measure;
    private readonly DiscordNotificationType $type;
    private readonly WebhookInterface $webhook;

    public function __construct(FlowMeasure $measure, DiscordNotificationType $type, WebhookInterface $webhook)
    {
        $this->measure = $measure;
        $this->type = $type;
        $this->webhook = $webhook;
    }

    // TODO: Update this
    public function isReissuedNotification(): bool
    {
        if (
            $this->type !== DiscordNotificationType::FLOW_MEASURE_ACTIVATED &&
            $this->type !== DiscordNotificationType::FLOW_MEASURE_NOTIFIED
        ) {
            return false;
        }

        $notificationsOfType = $this->measure->activatedAndNotifiedDivisionNotifications()
            ->where('division_discord_webhook_id', $this->webhook->id())
            ->get();

        return $notificationsOfType->filter(
            fn (
                DivisionDiscordNotification $notification
            ) => $notification->pivot->notified_as !== $this->measure->identifier
        )->isNotEmpty() && $notificationsOfType->filter(
            fn (
                DivisionDiscordNotification $notification
            ) => $notification->pivot->notified_as === $this->measure->identifier
        )->isEmpty();
    }

    public function measure(): FlowMeasure
    {
        return $this->measure;
    }

    public function type(): DiscordNotificationType
    {
        return $this->type;
    }
}
