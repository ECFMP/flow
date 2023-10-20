<?php

namespace Tests\Discord\FlowMeasure\Webhook;

use App\Discord\FlowMeasure\Webhook\Filter\FilterInterface;
use App\Discord\FlowMeasure\Webhook\WebhookMapper;
use App\Discord\Webhook\WebhookInterface;
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

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = Mockery::mock(FilterInterface::class);
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
            ->never();

        $this->assertEmpty($this->mapper->mapToWebhooks($flowMeasure));
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

        $this->assertCount(2, $this->mapper->mapToWebhooks($flowMeasure));
        $this->assertEquals(
            DivisionDiscordWebhook::all()->pluck('id'),
            $this->mapper->mapToWebhooks($flowMeasure)->map(fn (WebhookInterface $webhook) => $webhook->id())
        );
    }

    public function testItDeduplicatesWebhooks()
    {
        $webhook1 = DivisionDiscordWebhook::factory()->create();
        $webhook2 = DivisionDiscordWebhook::factory()->create();

        $flowMeasure = FlowMeasure::factory()->create();
        $fir = FlightInformationRegion::factory()->afterCreating(
            function (FlightInformationRegion $flightInformationRegion) use ($webhook1, $webhook2) {
                $flightInformationRegion->divisionDiscordWebhooks()->sync(
                    [
                        $webhook1->id,
                        $webhook2->id,
                    ]
                );
            }
        )->create();

        $fir2 = FlightInformationRegion::factory()->afterCreating(
            function (FlightInformationRegion $flightInformationRegion) use ($webhook1) {
                $flightInformationRegion->divisionDiscordWebhooks()->sync(
                    [
                        $webhook1->id,
                    ]
                );
            }
        )->create();

        $flowMeasure->notifiedFlightInformationRegions()->sync(
            [
                $fir->id,
                $fir2->id,
            ]
        );

        $this->filter->shouldReceive('shouldUseWebhook')
            ->andReturnTrue();

        $this->assertCount(2, $this->mapper->mapToWebhooks($flowMeasure));
        $this->assertEquals(
            new Collection([$webhook1->id, $webhook2->id]),
            $this->mapper->mapToWebhooks($flowMeasure)->map(fn (WebhookInterface $webhook) => $webhook->id())
        );
    }
}
