<?php

namespace Tests\Console\Commands;

use App\Console\Commands\UpdateNetworkData;
use App\Vatsim\NetworkDataDownloader;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;

class UpdateNetworkDataTest extends TestCase
{
    public function testItUpdatesNetworkData()
    {
        $downloaderMock = Mockery::mock(NetworkDataDownloader::class);
        $downloaderMock->shouldReceive('updateNetworkData')->once();
        $this->app->instance(NetworkDataDownloader::class, $downloaderMock);

        Artisan::call(UpdateNetworkData::class);
    }
}
