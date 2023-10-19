<?php

namespace Tests\Discord\FlowMeasure\Content;

use App\Discord\FlowMeasure\Content\DivisionWebhookRecipients;
use App\Discord\FlowMeasure\Content\EcfmpInterestedParties;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsFactory;
use App\Discord\FlowMeasure\Content\NoRecipients;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Discord\FlowMeasure\Provider\PendingWebhookMessageInterface;
use App\Discord\Webhook\WebhookInterface;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
use App\Models\DiscordTag;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class FlowMeasureRecipientsFactoryTest extends TestCase
{
    private readonly FlowMeasureRecipientsFactory $factory;
    private readonly FlowMeasure $flowMeasure;
    private readonly PendingWebhookMessageInterface $pendingWebhookMessage;
    private readonly PendingMessageInterface $pendingEcfmpMessage;
    private readonly WebhookInterface $webhook;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new FlowMeasureRecipientsFactory();
        $this->flowMeasure = FlowMeasure::factory()->create();
        $this->webhook = Mockery::mock(WebhookInterface::class);
        $this->pendingWebhookMessage = Mockery::mock(PendingWebhookMessageInterface::class);
        $this->pendingWebhookMessage
            ->shouldReceive('flowMeasure')
            ->andReturn($this->flowMeasure);
        $this->pendingWebhookMessage
            ->shouldReceive('webhook')
            ->andReturn($this->webhook);

        $this->pendingEcfmpMessage = Mockery::mock(PendingMessageInterface::class);
        $this->pendingEcfmpMessage
            ->shouldReceive('flowMeasure')
            ->andReturn($this->flowMeasure);
    }

    public function testItReturnsNoDivisionRecipients()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $this->assertInstanceOf(NoRecipients::class, $this->factory->makeRecipients($this->pendingWebhookMessage));
    }

    public function testItReturnsNoDivisionRecipientsIfTagNull()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $fir = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => null]]);
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $this->assertInstanceOf(NoRecipients::class, $this->factory->makeRecipients($this->pendingWebhookMessage));
    }

    public function testItReturnsNoDivisionRecipientsIfTagEmptyString()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $fir = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => '']]);
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $this->assertInstanceOf(NoRecipients::class, $this->factory->makeRecipients($this->pendingWebhookMessage));
    }

    public function testItReturnsNoDivisionRecipientsIfNotifiedRecently()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => '1234']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $notification = $this->flowMeasure->divisionDiscordNotifications()->create(
            [
                'content' => '',
                'embeds' => [],
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $this->flowMeasure->identifier,
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(55);
        $notification->save();

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(NoRecipients::class, $recipients);
    }

    public function testItReturnsDivisionRecipients()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $fir = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => '1234']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertEquals('<@1234>', $recipients->toString());
    }

    public function testItReturnsMultipleDivisionRecipients()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $fir = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->attach([$fir->id => ['tag' => '1234']]);
        $fir2 = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->attach([$fir2->id => ['tag' => '5678']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id, $fir2->id]);

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertEquals('<@1234> <@5678>', $recipients->toString());
    }

    public function testItIgnoresDivisionRecipientsThatAreNotForFlowMeasure()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $fir = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->attach([$fir->id => ['tag' => '1234']]);
        $fir2 = FlightInformationRegion::factory()->create();
        $divisionWebhook->flightInformationRegions()->attach([$fir2->id => ['tag' => '5678']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir2->id]);

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertEquals('<@5678>', $recipients->toString());
    }

    public function testItReturnsDivisionRecipientsIfNotifiedALongTimeAgo()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => '1234']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $notification = $this->flowMeasure->divisionDiscordNotifications()->create(
            [
                'content' => '',
                'embeds' => [],
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $this->flowMeasure->identifier,
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(61);
        $notification->save();

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertStringContainsString('<@1234>', $recipients->toString());
    }

    public function testItReturnsDivisionRecipientsIfNotifiedAsReissue()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => '1234']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $notification = $this->flowMeasure->divisionDiscordNotifications()->create(
            [
                'content' => '',
                'embeds' => [],
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => 'Not this',
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(55);
        $notification->save();

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertStringContainsString('<@1234>', $recipients->toString());
    }

    public function testItReturnsDivisionRecipientsIfNotActivating()
    {
        $this->pendingWebhookMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();
        $divisionWebhook->flightInformationRegions()->sync([$fir->id => ['tag' => '1234']]);
        $this->webhook->shouldReceive('id')->andReturn($divisionWebhook->id);

        $notification = $this->flowMeasure->divisionDiscordNotifications()->create(
            [
                'content' => '',
                'embeds' => [],
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $this->flowMeasure->identifier,
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(55);
        $notification->save();

        $recipients = $this->factory->makeRecipients($this->pendingWebhookMessage);
        $this->assertInstanceOf(DivisionWebhookRecipients::class, $recipients);
        $this->assertStringContainsString('<@1234>', $recipients->toString());
    }

    public function testItReturnsEcfmpRecipients()
    {
        $this->pendingEcfmpMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $recipients = $this->factory->makeEcfmpRecipients($this->pendingEcfmpMessage);
        $this->assertInstanceOf(EcfmpInterestedParties::class, $recipients);
        $this->assertStringContainsString(sprintf('<@%s>', $tag->tag), $recipients->toString());
    }

    public function testItReturnsNoEcfmpRecipientsIfNotifiedRecently()
    {
        $this->pendingEcfmpMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $notification = $this->flowMeasure->discordNotifications()->create(
            [
                'remote_id' => 'abc',
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $this->flowMeasure->identifier,
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(55);
        $notification->save();

        $recipients = $this->factory->makeEcfmpRecipients($this->pendingEcfmpMessage);
        $this->assertInstanceOf(NoRecipients::class, $recipients);
    }

    public function testItReturnsEcfmpRecipientsIfNotifiedALongTimeAgo()
    {
        $this->pendingEcfmpMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $notification = $this->flowMeasure->discordNotifications()->create(
            [
                'remote_id' => 'abc',
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $this->flowMeasure->identifier,
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(61);
        $notification->save();

        $recipients = $this->factory->makeEcfmpRecipients($this->pendingEcfmpMessage);
        $this->assertInstanceOf(EcfmpInterestedParties::class, $recipients);
        $this->assertStringContainsString($tag->tag, $recipients->toString());
    }

    public function testItReturnsEcfmpRecipientsIfNotifiedAsReissue()
    {
        $this->pendingEcfmpMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $notification = $this->flowMeasure->discordNotifications()->create(
            [
                'remote_id' => 'abc',
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => 'Not this',
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(55);
        $notification->save();

        $recipients = $this->factory->makeEcfmpRecipients($this->pendingEcfmpMessage);
        $this->assertInstanceOf(EcfmpInterestedParties::class, $recipients);
        $this->assertStringContainsString($tag->tag, $recipients->toString());
    }

    public function testItReturnsEcfmpRecipientsIfNotActivating()
    {
        $this->pendingEcfmpMessage
            ->shouldReceive('type')
            ->andReturn(DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN);
        $fir = FlightInformationRegion::factory()->has(DiscordTag::factory()->withoutAtSymbol()->count(1))->create();
        $tag = $fir->discordTags->first();
        $this->flowMeasure->notifiedFlightInformationRegions()->sync([$fir->id]);

        $notification = $this->flowMeasure->discordNotifications()->create(
            [
                'remote_id' => 'abc',
            ],
            [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $this->flowMeasure->identifier,
            ]
        );
        $notification->created_at = Carbon::now()->subMinutes(55);
        $notification->save();

        $recipients = $this->factory->makeEcfmpRecipients($this->pendingEcfmpMessage);
        $this->assertInstanceOf(EcfmpInterestedParties::class, $recipients);
        $this->assertStringContainsString($tag->tag, $recipients->toString());
    }
}
