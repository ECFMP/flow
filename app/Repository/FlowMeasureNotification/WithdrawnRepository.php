<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WithdrawnRepository implements RepositoryInterface
{
    public function flowMeasuresForNotification(): Collection
    {
        return $this->baseQuery()->notified()
            ->union($this->baseQuery()->active())
            ->orderBy('id')
            ->get();
    }

    private function baseQuery(): Builder
    {
        return FlowMeasure::with('discordNotifications')
            ->onlyTrashed()
            ->where('deleted_at', '>', Carbon::now()->subHour());
    }

    public function notificationType(): DiscordNotificationType
    {
        return DiscordNotificationType::FLOW_MEASURE_WITHDRAWN;
    }
}
