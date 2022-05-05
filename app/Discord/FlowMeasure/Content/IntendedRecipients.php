<?php

namespace App\Discord\FlowMeasure\Content;

use App\Models\FlightInformationRegion;
use Arr;
use Illuminate\Support\Collection;

class IntendedRecipients extends AbstractFlowMeasureContent
{
    public function toString(): string
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
            ->map(fn (string $tag) => sprintf('<%s>', $tag))
            ->unique()
            ->values();
    }
}
