<?php

namespace Tests\Vatsim;

use App\Vatsim\NetworkDataDownloader;
use App\Vatsim\Processor\Pilot\PilotProcessor;
use App\Vatsim\Processor\VatsimDataProcessorInterface;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class NetworkDataDownloaderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::set('services.vatsim_data.url', 'https://data.vatsim.net/v3/vatsim-data.json');
    }

    public function testItHasProcessors()
    {
        $downloader = $this->app->make(NetworkDataDownloader::class);
        $this->assertEquals(
            [PilotProcessor::class],
            array_map(fn(VatsimDataProcessorInterface $processor) => get_class($processor), $downloader->processors())
        );
    }

    public function testItDownloadsDataAndCallsProcessors()
    {
        Http::fake(
            [
                'https://data.vatsim.net/v3/vatsim-data.json' => Http::response(['foo' => 'bar']),
            ]
        );

        $mockProcessor = Mockery::mock(VatsimDataProcessorInterface::class);
        $mockProcessor->shouldReceive('processNetworkData')->with(['foo' => 'bar'])->once();

        $downloader = $this->app->make(NetworkDataDownloader::class, ['processors' => [$mockProcessor]]);

        $downloader->updateNetworkData();

        Http::assertSentCount(1);
        Http::assertSent(
            fn(Request $request) => $request->url() === 'https://data.vatsim.net/v3/vatsim-data.json' &&
            $request->method() === 'GET'
        );
    }

    public function testItDoesntCallProcessorsIfDataFetchFails()
    {
        Http::fake(
            [
                'https://data.vatsim.net/v3/vatsim-data.json' => Http::response(['foo' => 'bar'], 500),
            ]
        );

        $mockProcessor = Mockery::mock(VatsimDataProcessorInterface::class);
        $mockProcessor->shouldNotReceive('processNetworkData');

        $downloader = $this->app->make(NetworkDataDownloader::class, ['processors' => [$mockProcessor]]);

        $downloader->updateNetworkData();

        Http::assertSentCount(1);
        Http::assertSent(
            fn(Request $request) => $request->url() === 'https://data.vatsim.net/v3/vatsim-data.json' &&
            $request->method() === 'GET'
        );
    }
}
