<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\FlowMeasure\Provider\PendingWebhookMessageInterface;
use App\Discord\Message\Tag\Tag;
use App\Enums\DiscordNotificationType;
use App\Models\DiscordNotification;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordTag;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use Carbon\Carbon;

class FlowMeasureRecipientsFactory
{
    public function makeRecipients(PendingWebhookMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        if ($this->hasRecentlyBeenNotifiedToWebhook($pendingMessage)) {
            return new NoRecipients();
        }

        return $this->divisionRecipients($pendingMessage);
    }

    public function makeEcfmpRecipients(PendingMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        if ($this->hasRecentlyBeenNotified($pendingMessage)) {
            return new NoRecipients();
        }

        return $this->ecfmpRecipients($pendingMessage);
    }

    private function hasRecentlyBeenNotifiedToWebhook(PendingWebhookMessageInterface $pendingMessage): bool
    {
        $measure = $pendingMessage->flowMeasure();
        return $pendingMessage->type(
        ) === DiscordNotificationType::FLOW_MEASURE_ACTIVATED && $measure->notifiedDivisionNotifications->firstWhere(
            fn (DivisionDiscordNotification $notification) => $notification->created_at > Carbon::now()->subHour() &&
                $notification->pivot->notified_as === $measure->identifier
        ) !== null;
    }

    private function hasRecentlyBeenNotified(PendingMessageInterface $pendingMessage): bool
    {
        $measure = $pendingMessage->flowMeasure();
        return $pendingMessage->type(
        ) === DiscordNotificationType::FLOW_MEASURE_ACTIVATED && $measure->notifiedEcfmpNotifications->firstWhere(
            fn (DiscordNotification $notification) => $notification->created_at > Carbon::now()->subHour() &&
                $notification->pivot->notified_as === $measure->identifier
        ) !== null;
    }

    private function ecfmpRecipients(PendingMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        return new EcfmpInterestedParties(
            $pendingMessage->flowMeasure()->notifiedFlightInformationRegions
                ->map(fn (FlightInformationRegion $flightInformationRegion) => $flightInformationRegion->discordTags)
                ->flatten()
                ->map(fn (DiscordTag $discordTag) => new Tag($discordTag))
        );
    }

    private function divisionRecipients(PendingWebhookMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        $recipients = DivisionDiscordWebhook::find($pendingMessage->webhook()->id())
            ->flightInformationRegions
            ->filter(
                fn (FlightInformationRegion $flightInformationRegion) => $pendingMessage
                    ->flowMeasure()
                    ->notifiedFlightInformationRegions
                    ->firstWhere(fn (FlightInformationRegion $notifiedFir) => $notifiedFir->id === $flightInformationRegion->id)
            )
            ->filter(fn (FlightInformationRegion $flightInformationRegion) => !empty($flightInformationRegion->pivot->tag))
            ->map(fn (FlightInformationRegion $flightInformationRegion) => new Tag($flightInformationRegion->pivot));

        return $recipients->isEmpty()
            ? new NoRecipients()
            : new DivisionWebhookRecipients($recipients);
    }
}
