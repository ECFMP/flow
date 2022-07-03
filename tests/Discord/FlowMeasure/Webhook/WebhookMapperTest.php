<?php

namespace Tests\Discord\FlowMeasure\Webhook;

use App\Discord\FlowMeasure\Webhook\Filter\FilterInterface;
use App\Discord\FlowMeasure\Webhook\WebhookMapper;
use App\Discord\Webhook\EcfmpWebhook;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class WebhookMapperTest extends TestCase
{
    private readonly FilterInterface $filter;
    private readonly WebhookMapper $mapper;
    private readonly EcfmpWebhook $ecfmpWebhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = Mockery::mock(FilterInterface::class);
        $this->ecfmpWebhook = $this->app->make(EcfmpWebhook::class);
        $this->mapper = $this->app->make(
            WebhookMapper::class,
            [
                'filter' => $this->filter,
            ]
        );
    }

    public function testItReturnsEmptyCollectionWhenAllWebhooksAreFiltered()
    {
        $flowMeasure = FlowMeasure::factory()->create();
        $this->filter->shouldReceive('shouldUseWebhook')
            ->with($flowMeasure, $this->ecfmpWebhook)
            ->once()
            ->andReturnFalse();

        $this->assertEmpty($this->mapper->mapToWebhooks($flowMeasure));
    }

    public function testItReturnsJustEcfmpWebhookIfNoDivisionWebhooks()
    {
        $flowMeasure = FlowMeasure::factory()->create();
        $this->filter->shouldReceive('shouldUseWebhook')
            ->with($flowMeasure, $this->ecfmpWebhook)
            ->once()
            ->andReturnTrue();

        $this->assertEquals(new Collection([$this->ecfmpWebhook]), $this->mapper->mapToWebhooks($flowMeasure));
    }

    public function testItFiltersDivisionWebhooks()
    {
        $flowMeasure = FlowMeasure::factory()->create();
        $fir1 = FlightInformationRegion::factory()->afterCreating(
            function (FlightInformationRegion $flightInformationRegion) {
                $flightInformationRegion->divisionDiscordWebhooks()->sync(
                    DivisionDiscordWebhook::factory()->count(2)->create(),
                );
            }
        )->create();
        $fir2 = FlightInformationRegion::factory()->afterCreating(
            function (FlightInformationRegion $flightInformationRegion) {
                $flightInformationRegion->divisionDiscordWebhooks()->sync(
                    DivisionDiscordWebhook::factory()->count(2)->create(),
                );
            }
        )->create();

        $flowMeasure->notifiedFlightInformationRegions()->sync(
            [
                $fir1->id,
                $fir2->id,
            ]
        );

        $this->filter->shouldReceive('shouldUseWebhook')
            ->andReturnFalse();

        $this->assertEmpty($this->mapper->mapToWebhooks($flowMeasure));
    }

    public function testItReturnsAllWebhooks()
    {
        $flowMeasure = FlowMeasure::factory()->create();
        $fir = FlightInformationRegion::factory()->afterCreating(
            function (FlightInformationRegion $flightInformationRegion) {
                $flightInformationRegion->divisionDiscordWebhooks()->sync(
                    DivisionDiscordWebhook::factory()->count(2)->create(),
                );
            }
        )->create();

        $flowMeasure->notifiedFlightInformationRegions()->sync(
            [
                $fir->id,
            ]
        );

        $this->filter->shouldReceive('shouldUseWebhook')
            ->andReturnTrue();

        $this->assertCount(3, $this->mapper->mapToWebhooks($flowMeasure));
    }
}
