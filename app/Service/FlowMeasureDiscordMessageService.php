<?php

namespace App\Service;

use App\Discord\DiscordInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedWithoutRecipientsMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureNotifiedMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureExpiredMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureWithdrawnMessage;
use App\Discord\Message\MessageInterface;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\NoReturn;

class FlowMeasureDiscordMessageService
{
    private readonly DiscordInterface $discord;

    public function __construct(DiscordInterface $discord)
    {
        $this->discord = $discord;
    }

    public function sendMeasureNotifiedDiscordNotifications(): void
    {
        FlowMeasure::whereDoesntHave('notifiedDiscordNotifications', function (Builder $builder) {
            $builder->where('discord_notification_flow_measure.notified_as', '=', DB::raw('flow_measures.identifier'));
        })
            ->whereDoesntHave('activatedDiscordNotifications')
            ->where('start_time', '<', Carbon::now()->addDay())
            ->where('start_time', '>', Carbon::now())
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED,
                    new FlowMeasureNotifiedMessage($flowMeasure)
                );
            });
    }

    public function sendMeasureActivatedDiscordNotifications(): void
    {
        FlowMeasure::with('notifiedDiscordNotifications')
            ->whereDoesntHave('activatedDiscordNotifications', function (Builder $builder) {
                $builder->where(
                    'discord_notification_flow_measure.notified_as',
                    '=',
                    DB::raw('flow_measures.identifier')
                );
            })
            ->active()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED,
                    $this->notifiedMessageSentWithinTheLastHour($flowMeasure)
                        ? new FlowMeasureActivatedWithoutRecipientsMessage($flowMeasure)
                        : new FlowMeasureActivatedMessage($flowMeasure)
                );
            });
    }

    private function notifiedMessageSentWithinTheLastHour(FlowMeasure $measure): bool
    {
        return $measure->notifiedDiscordNotifications->firstWhere(
                fn(DiscordNotification $notification) => $notification->created_at > Carbon::now()->subHour() &&
                    $notification->pivot->notified_as === $measure->identifier
            ) !== null;
    }

    public function sendMeasureWithdrawnDiscordNotifications(): void
    {
        FlowMeasure::whereHas('activatedDiscordNotifications')
            ->whereDoesntHave('withdrawnDiscordNotifications')
            ->onlyTrashed()
            ->active()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN,
                    new FlowMeasureWithdrawnMessage($flowMeasure)
                );
            });
    }

    public function sendMeasureExpiredDiscordNotifications(): void
    {
        FlowMeasure::whereHas('activatedDiscordNotifications')
            ->whereDoesntHave('withdrawnAndExpiredDiscordNotifications')
            ->withTrashed()
            ->expired()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    $flowMeasure->trashed()
                        ? DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                        : DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED,
                    $flowMeasure->trashed()
                        ? new FlowMeasureWithdrawnMessage($flowMeasure)
                        : new FlowMeasureExpiredMessage($flowMeasure)
                );
            });
    }

    #[NoReturn] private function sendDiscordNotification(
        FlowMeasure $flowMeasure,
        DiscordNotificationTypeEnum $type,
        MessageInterface $message
    ): void {
        $notification = $flowMeasure->discordNotifications()->create(
            [
                'content' => $message->content(),
                'embeds' => $message->embeds()->toArray(),
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum($type),
                'notified_as' => $flowMeasure->identifier,
            ]
        );
        activity()
            ->inLog('Discord')
            ->performedOn($notification)
            ->event(sprintf('%s - %s', $flowMeasure->identifier, $type->name()))
            ->causedByAnonymous()
            ->withProperties(
                [
                    'type' => $type->name(),
                    'content' => $message->content(),
                    'embeds' => json_encode($message->embeds()->toArray()),
                ]
            )
            ->log('Sending discord notification');
        $this->discord->sendMessage($message);
    }
}
