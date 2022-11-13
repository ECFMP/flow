<?php

namespace App\Vatsim;

use App\Vatsim\Processor\VatsimDataProcessorInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NetworkDataDownloader
{
    /**
     * @var VatsimDataProcessorInterface[]
     */
    private readonly array $processors;

    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    public function updateNetworkData(): void
    {
        $networkDataResponse = Http::withUserAgent(config('app.name'))
            ->timeout(10)
            ->get(config('services.vatsim_data.url'));

        if (!$networkDataResponse->successful()) {
            Log::error(sprintf('Failed to download VATSIM data, status code was %d', $networkDataResponse->status()));
            return;
        }

        foreach ($this->processors as $processor) {
            $processor->processNetworkData($networkDataResponse->json());
        }
    }
}
