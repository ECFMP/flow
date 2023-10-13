<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class ExpiredRepository implements RepositoryInterface
{
    public function flowMeasuresToBeSentToEcfmp(): Collection
    {
        return FlowMeasure::expiredRecently()
            ->withoutEcfmpNotificationOfTypes([
                DiscordNotificationType::FLOW_MEASURE_EXPIRED,
                DiscordNotificationType::FLOW_MEASURE_WITHDRAWN,
            ])
            ->get();
    }

    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('divisionDiscordNotifications')
            ->expiredRecently();
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_EXPIRED;
    }
}
