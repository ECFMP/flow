<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\Message\Tag\Tag;
use App\Enums\DiscordNotificationType;
use App\Models\DivisionDiscordNotification;
use App\Models\DiscordTag;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use Carbon\Carbon;

class FlowMeasureRecipientsFactory
{
    public function makeRecipients(PendingMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        if ($this->hasRecentlyBeenNotified($pendingMessage)) {
            return new NoRecipients();
        }

        return $pendingMessage->webhook()->id() === null
            ? $this->ecfmpRecipients($pendingMessage)
            : $this->divisionRecipients($pendingMessage);
    }

    private function hasRecentlyBeenNotified(PendingMessageInterface $pendingMessage): bool
    {
        $measure = $pendingMessage->flowMeasure();
        return $pendingMessage->type(
        ) === DiscordNotificationType::FLOW_MEASURE_ACTIVATED && $measure->notifiedDiscordNotifications->firstWhere(
                fn(DivisionDiscordNotification $notification) => $notification->created_at > Carbon::now()->subHour() &&
                $notification->pivot->notified_as === $measure->identifier
            ) !== null;
    }

    private function ecfmpRecipients(PendingMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        return new EcfmpInterestedParties(
            $pendingMessage->flowMeasure()->notifiedFlightInformationRegions
                ->map(fn(FlightInformationRegion $flightInformationRegion) => $flightInformationRegion->discordTags)
                ->flatten()
                ->map(fn(DiscordTag $discordTag) => new Tag($discordTag))
        );
    }

    private function divisionRecipients(PendingMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        $recipients = DivisionDiscordWebhook::find($pendingMessage->webhook()->id())
            ->flightInformationRegions
            ->filter(
                fn(FlightInformationRegion $flightInformationRegion) => $pendingMessage
                    ->flowMeasure()
                    ->notifiedFlightInformationRegions
                    ->firstWhere(fn(FlightInformationRegion $notifiedFir) => $notifiedFir->id === $flightInformationRegion->id)
            )
            ->filter(fn(FlightInformationRegion $flightInformationRegion) => !empty($flightInformationRegion->pivot->tag))
            ->map(fn(FlightInformationRegion $flightInformationRegion) => new Tag($flightInformationRegion->pivot));

        return $recipients->isEmpty()
            ? new NoRecipients()
            : new DivisionWebhookRecipients($recipients);
    }
}
