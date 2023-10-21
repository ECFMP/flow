<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\DiscordNotification;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExpiredRepository implements RepositoryInterface
{
    public function flowMeasuresToBeSentToEcfmp(): Collection
    {
        $webhooksSentInLastTwoHours = DiscordNotification::where('created_at', '>=', Carbon::now()->subHours(2))
            ->count();

        if ($webhooksSentInLastTwoHours > 5) {
            return collect();
        }

        return FlowMeasure::expiredRecently()
            ->where('revision_number', '<', 2)
            ->withEcfmpNotificationOfTypes([
                DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
                DiscordNotificationType::FLOW_MEASURE_NOTIFIED,
            ])
            ->withoutEcfmpNotificationOfTypes([
                DiscordNotificationType::FLOW_MEASURE_EXPIRED,
                DiscordNotificationType::FLOW_MEASURE_WITHDRAWN,
            ])
            ->get()
            ->map(
                fn (FlowMeasure $measure) => new FlowMeasureForNotification(
                    $measure,
                    false
                )
            );
    }

    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('divisionDiscordNotifications')
            ->expiredRecently()
            ->get();
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_EXPIRED;
    }
}
