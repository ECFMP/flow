<?php

namespace App\Discord\FlowMeasure\Webhook;

use App\Discord\FlowMeasure\Webhook\Filter\FilterInterface;
use App\Discord\Webhook\EcfmpWebhook;
use App\Discord\Webhook\WebhookInterface;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;

class WebhookMapper implements MapperInterface
{
    private readonly FilterInterface $filter;
    private readonly EcfmpWebhook $ecfmpWebhook;

    public function __construct(FilterInterface $filter, EcfmpWebhook $ecfmpWebhook)
    {
        $this->filter = $filter;
        $this->ecfmpWebhook = $ecfmpWebhook;
    }

    public function mapToWebhooks(FlowMeasure $measure): Collection
    {
        return Collection::make([$this->ecfmpWebhook])
            ->merge(
                $measure->notifiedFlightInformationRegions->map(
                    fn(FlightInformationRegion $fir) => $fir->divisionDiscordWebhooks
                )->flatten()
            )
            ->filter(fn(WebhookInterface $webhook): bool => $this->filter->shouldUseWebhook($measure, $webhook))
            ->unique(fn(WebhookInterface $webhook): ?int => $webhook->id())
            ->values();
    }
}
