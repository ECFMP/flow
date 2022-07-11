<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class ExpiredRepository implements RepositoryInterface
{
    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('discordNotifications')
            ->expiredRecently()
            ->get();
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_EXPIRED;
    }
}
