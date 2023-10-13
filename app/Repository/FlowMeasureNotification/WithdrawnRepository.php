<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WithdrawnRepository implements RepositoryInterface
{
    public function flowMeasuresToBeSentToEcfmp(): Collection
    {
        return $this->baseQueryForEcfmp()->notified()
            ->union($this->baseQueryForEcfmp()->active())
            ->orderBy('id')
            ->get();
    }

    public function flowMeasuresForNotification(): Collection
    {
        return $this->baseQuery()->notified()
            ->union($this->baseQuery()->active())
            ->orderBy('id')
            ->get();
    }

    public function baseQueryForEcfmp(): Builder
    {
        return $this->baseQuery()
            ->WithEcfmpNotificationOfTypes([
                DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
                DiscordNotificationType::FLOW_MEASURE_NOTIFIED,
            ])
            ->withoutEcfmpNotificationOfTypes([
                DiscordNotificationType::FLOW_MEASURE_EXPIRED,
                DiscordNotificationType::FLOW_MEASURE_WITHDRAWN,
            ]);
    }

    private function baseQuery(): Builder
    {
        return FlowMeasure::with('divisionDiscordNotifications')
            ->onlyTrashed()
            ->where('deleted_at', '>', Carbon::now()->subHour());
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_WITHDRAWN;
    }
}
