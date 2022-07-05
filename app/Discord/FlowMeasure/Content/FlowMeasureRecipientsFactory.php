<?php

namespace App\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\Message\Tag\Tag;
use App\Models\DiscordTag;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;

class FlowMeasureRecipientsFactory
{
    public function makeRecipients(PendingMessageInterface $pendingMessage): FlowMeasureRecipientsInterface
    {
        return $pendingMessage->webhook()->id() === null
            ? $this->ecfmpRecipients($pendingMessage)
            : $this->divisionRecipients($pendingMessage);
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
        $divisionWebhook = DivisionDiscordWebhook::find($pendingMessage->webhook()->id());

        return $divisionWebhook->tag === ''
            ? new NoRecipients()
            : new DivisionWebhookRecipients(new Tag($divisionWebhook));
    }
}
