<?php

namespace App\Discord\FlowMeasure\Webhook;

use App\Discord\FlowMeasure\Webhook\Filter\FilterInterface;
use App\Discord\Webhook\WebhookInterface;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class WebhookMapper implements MapperInterface
{
    private readonly FilterInterface $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public function mapToWebhooks(FlowMeasure $measure): Collection
    {
        return Collection::make()
            ->merge(
                $measure->notifiedFlightInformationRegions->map(
                    fn (FlightInformationRegion $fir) => $fir->divisionDiscordWebhooks
                )->flatten()
            )
            ->filter(fn (WebhookInterface $webhook): bool => $this->filter->shouldUseWebhook($measure, $webhook))
            ->unique(fn (WebhookInterface $webhook): ?int => $webhook->id())
            ->values();
    }
}
