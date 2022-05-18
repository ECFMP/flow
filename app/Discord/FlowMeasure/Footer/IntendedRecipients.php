<?php

namespace App\Discord\FlowMeasure\Footer;

use App\Models\FlightInformationRegion;
use Arr;
use Illuminate\Support\Collection;
use Str;

class IntendedRecipients extends AbstractFlowMeasureFooter
{
    public function footer(): string
    {
        if ($this->flowMeasure->notifiedFlightInformationRegions->isEmpty()) {
            return '';
        }

        $firTags = $this->getNotifiedFlightInformationRegionTags();
        if ($firTags->isEmpty()) {
            return '';
        }

        return sprintf(
            '**FAO**: %s (acknowledge receipt with a :white_check_mark: reaction)',
            Arr::join($firTags->toArray(), ' ')
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
            ->map(fn (string $tag) => $this->formatTag($tag))
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
