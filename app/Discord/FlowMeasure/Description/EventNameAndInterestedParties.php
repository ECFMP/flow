<?php

namespace App\Discord\FlowMeasure\Description;

use App\Discord\Message\Emoji\Emoji;
use App\Models\FlightInformationRegion;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EventNameAndInterestedParties extends AbstractFlowMeasureDescription
{
    public function description(): string
    {
        if (!$this->flowMeasure->event) {
            return $this->formatNotifications();
        }

        return trim(
            sprintf(
                "%s\n\n%s",
                $this->flowMeasure->event->name,
                $this->formatNotifications()
            )
        );
    }

    private function formatNotifications(): string
    {
        if ($this->flowMeasure->notifiedFlightInformationRegions->isEmpty()) {
            return '';
        }

        $firTags = $this->getNotifiedFlightInformationRegionTags();
        if ($firTags->isEmpty()) {
            return '';
        }

        return sprintf(
            "**FAO**: %s\nPlease acknowledge receipt with a %s reaction.",
            Arr::join($firTags->toArray(), ' '),
            Emoji::WHITE_CHECK_MARK->value
        );
    }

    private function getNotifiedFlightInformationRegionTags(): Collection
    {
        return $this->flowMeasure->notifiedFlightInformationRegions
            ->map(
                fn(FlightInformationRegion $flightInformationRegion) => $flightInformationRegion->discordTags->pluck(
                    'tag'
                )
            )
            ->flatten()
            ->map(fn(string $tag) => $this->formatTag($tag))
            ->unique()
            ->values();
    }

    private function formatTag(string $tag): string
    {
        return sprintf(
            '<%s>',
            Str::startsWith($tag, '@') ? $tag : '@' . $tag
        );
    }
}
