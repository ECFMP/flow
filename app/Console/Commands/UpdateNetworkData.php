<?php

namespace App\Console\Commands;

use App\Vatsim\NetworkDataDownloader;
use Illuminate\Console\Command;

class UpdateNetworkData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network-data:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update local tables based on information from the VATSIM data feed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NetworkDataDownloader $networkDataDownloader): int
    {
        $this->info('Starting vatsim data update');
        $networkDataDownloader->updateNetworkData();
        $this->info('Vatsim data update complete');

        return 0;
    }
}
