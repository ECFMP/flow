<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\DivisionWebhookRecipients;
use App\Discord\FlowMeasure\Content\EcfmpInterestedParties;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsFactory;
use App\Discord\FlowMeasure\Content\NoRecipients;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\Webhook\WebhookInterface;
use App\Models\DiscordTag;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Mockery;
use Tests\TestCase;

class FlowMeasureRecipientsFactoryTest extends TestCase
{
    private readonly FlowMeasureRecipientsFactory $factory;
    private readonly FlowMeasure $flowMeasure;
    private readonly PendingMessageInterface $pendingMessage;
    private readonly WebhookInterface $webhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new FlowMeasureRecipientsFactory();
        $this->flowMeasure = FlowMeasure::factory()->create();
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->pendingMessage = Mockery::mock(PendingMessageInterface::class);
        $this->pendingMessage
            ->shouldReceive('webhook')
            ->andReturn($this->webhook);
        $this->pendingMessage
            ->shouldReceive('flowMeasure')
            ->andReturn($this->flowMeasure);
    }

    public function testItReturnsNoDivisionRecipients()
    {
        $divisionWebhook = DivisionDiscordWebhook::factory()->create(['tag' => '']);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $this->assertInstanceOf(NoRecipients::class, $this->factory->makeRecipients($this->pendingMessage));
    }

    public function testItReturnsDivisionRecipients()
    {
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $recipients = $this->factory->makeRecipients($this->pendingMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertEquals(sprintf('<%s>', $divisionWebhook->tag), $recipients->toString());
    }

    public function testItReturnsEcfmpRecipients()
    {
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $this->webhook->shouldReceive('id')->andReturn(null);

        $recipients = $this->factory->makeRecipients($this->pendingMessage);
        $this->assertInstanceOf(EcfmpInterestedParties::class, $recipients);
        $this->assertStringContainsString(sprintf('<@%s>', $tag->tag), $recipients->toString());
    }
}
