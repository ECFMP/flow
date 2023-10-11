<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotifiedRepository implements RepositoryInterface
{
    public function flowMeasuresForNotification(): Collection
    {
        return FlowMeasure::with('divisionDiscordNotifications')
            ->where('start_time', '<', Carbon::now()->addDay())
            ->where('start_time', '>', Carbon::now())
            ->get();
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_NOTIFIED;
    }
}
