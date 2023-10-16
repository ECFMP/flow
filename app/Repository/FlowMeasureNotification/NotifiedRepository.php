<?php

namespace App\Repository\FlowMeasureNotification;

use App\Enums\DiscordNotificationType;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class NotifiedRepository implements RepositoryInterface
{
    public function flowMeasuresToBeSentToEcfmp(): Collection
    {
        return FlowMeasure::where('start_time', '<', Carbon::now()->addDay())
            ->where('start_time', '>', Carbon::now())
            ->withoutEcfmpNotificationOfTypeForIdentifier($this->notificationType())
            ->withoutEcfmpNotificationOfType(DiscordNotificationType::FLOW_MEASURE_ACTIVATED)
            ->select('flow_measures.*')
            ->selectSub(
                function (Builder $query) {
                    $query->selectRaw('COUNT(*)')
                        ->from('discord_notification_flow_measure', 'previous_notifications_for_identifier')
                        ->whereRaw('previous_notifications_for_identifier.flow_measure_id = flow_measures.id')
                        ->whereRaw('previous_notifications_for_identifier.notified_as = flow_measures.identifier');
                },
                'count_previous_for_identifier'
            )
            ->selectSub(
                function (Builder $query) {
                    $query->selectRaw('COUNT(*)')
                        ->from('discord_notification_flow_measure', 'previous_notifications_for_other_identifier')
                        ->whereRaw('previous_notifications_for_other_identifier.flow_measure_id = flow_measures.id')
                        ->whereRaw('previous_notifications_for_other_identifier.notified_as <> flow_measures.identifier');
                },
                'count_previous_other_identifiers'
            )
            ->get()
            ->map(
                fn (FlowMeasure $measure) => new FlowMeasureForNotification(
                    $measure,
                    $measure->count_previous_for_identifier === 0 && $measure->count_previous_other_identifiers !== 0
                )
            );
    }

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
