<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class ActiveRepository implements RepositoryInterface
{
    public function flowMeasuresToBeSentToEcfmp(): Collection
    {
        return FlowMeasure::active()
            ->withoutEcfmpNotificationOfTypeForIdentifier($this->notificationType())
            ->get();
    }

    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('divisionDiscordNotifications')
            ->active()
            ->get();
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_ACTIVATED;
    }
}
