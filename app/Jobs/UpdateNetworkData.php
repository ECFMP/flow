<?php

namespace App\Jobs;

use App\Vatsim\NetworkDataDownloader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateNetworkData implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private readonly NetworkDataDownloader $networkDataDownloader;

    public function __construct(NetworkDataDownloader $networkDataDownloader)
    {
        $this->networkDataDownloader = $networkDataDownloader;
    }

    public function handle(): void
    {
        Log::info('Starting vatsim data update');
        $this->networkDataDownloader->updateNetworkData();
        Log::info('Vatsim data update complete');
    }
}
