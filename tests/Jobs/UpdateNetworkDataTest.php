<?php

namespace Tests\Jobs;

use App\Jobs\UpdateNetworkData;
use App\Vatsim\NetworkDataDownloader;
use Mockery;
use Tests\TestCase;

class UpdateNetworkDataTest extends TestCase
{
    public function testItUpdatesNetworkData()
    {
        $downloaderMock = Mockery::mock(NetworkDataDownloader::class);
        $downloaderMock->shouldReceive('updateNetworkData')->once();
        $this->app->instance(NetworkDataDownloader::class, $downloaderMock);

        $job = $this->app->make(UpdateNetworkData::class);
        $job->handle();
    }
}
