<?php

namespace App\Service;

use App\Discord\DiscordInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedWithoutRecipientsMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureNotifiedMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureExpiredMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureWithdrawnMessage;
use App\Discord\Message\MessageInterface;
use App\Enums\DiscordNotificationType;
use App\Models\DiscordNotification;
use App\Models\FlowMeasure;
use Carbon\Carbon;
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
        FlowMeasure::whereDoesntHave('discordNotifications', function (Builder $notification) {
            $notification->types(
                [DiscordNotificationType::FLOW_MEASURE_ACTIVATED, DiscordNotificationType::FLOW_MEASURE_NOTIFIED]
            );
        })
            ->where('start_time', '<', Carbon::now()->addDay())
            ->where('start_time', '>', Carbon::now())
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationType::FLOW_MEASURE_NOTIFIED,
                    new FlowMeasureNotifiedMessage($flowMeasure)
                );
            });
    }

    public function sendMeasureActivatedDiscordNotifications(): void
    {
        FlowMeasure::with('discordNotifications')
            ->whereDoesntHave('discordNotifications', function (Builder $notification) {
                $notification->type(DiscordNotificationType::FLOW_MEASURE_ACTIVATED);
            })
            ->active()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
                    $this->notifiedMessageSentWithinTheLastHour($flowMeasure)
                        ? new FlowMeasureActivatedWithoutRecipientsMessage($flowMeasure)
                        : new FlowMeasureActivatedMessage($flowMeasure)
                );
            });
    }

    private function notifiedMessageSentWithinTheLastHour(FlowMeasure $measure): bool
    {
        return $measure->discordNotifications->firstWhere(
                fn(DiscordNotification $notification
                ) => $notification->type === DiscordNotificationType::FLOW_MEASURE_NOTIFIED &&
                    $notification->created_at > Carbon::now()->subHour()
            ) !== null;
    }

    public function sendMeasureWithdrawnDiscordNotifications(): void
    {
        FlowMeasure::whereHas('discordNotifications', function (Builder $notification) {
            $notification->type(DiscordNotificationType::FLOW_MEASURE_ACTIVATED);
        })->whereDoesntHave('discordNotifications', function (Builder $notification) {
            $notification->type(DiscordNotificationType::FLOW_MEASURE_WITHDRAWN);
        })
            ->onlyTrashed()
            ->active()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    DiscordNotificationType::FLOW_MEASURE_WITHDRAWN,
                    new FlowMeasureWithdrawnMessage($flowMeasure)
                );
            });
    }

    public function sendMeasureExpiredDiscordNotifications(): void
    {
        FlowMeasure::whereHas('discordNotifications', function (Builder $notification) {
            $notification->type(DiscordNotificationType::FLOW_MEASURE_ACTIVATED);
        })->whereDoesntHave('discordNotifications', function (Builder $notification) {
            $notification->types(
                [DiscordNotificationType::FLOW_MEASURE_WITHDRAWN, DiscordNotificationType::FLOW_MEASURE_EXPIRED]
            );
        })
            ->withTrashed()
            ->expired()
            ->get()
            ->each(function (FlowMeasure $flowMeasure) {
                $this->sendDiscordNotification(
                    $flowMeasure,
                    $flowMeasure->trashed()
                        ? DiscordNotificationType::FLOW_MEASURE_WITHDRAWN
                        : DiscordNotificationType::FLOW_MEASURE_EXPIRED,
                    $flowMeasure->trashed()
                        ? new FlowMeasureWithdrawnMessage($flowMeasure)
                        : new FlowMeasureExpiredMessage($flowMeasure)
                );
            });
    }

    #[NoReturn] private function sendDiscordNotification(
        FlowMeasure $flowMeasure,
        DiscordNotificationType $type,
        MessageInterface $message
    ): void {
        $flowMeasure->discordNotifications()->create(
            [
                'type' => $type,
                'content' => $message->content(),
            ]
        );
        $this->discord->sendMessage($message);
    }
}
