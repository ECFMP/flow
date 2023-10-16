<?php

namespace Tests\Discord\FlowMeasure\Sender;

use App\Discord\DiscordServiceInterface;
use App\Discord\Exception\DiscordServiceException;
use App\Discord\FlowMeasure\Content\FlowMeasureRecipientsInterface;
use App\Discord\FlowMeasure\Embed\FlowMeasureEmbedInterface;
use App\Discord\FlowMeasure\Helper\NotificationReissuerInterface;
use App\Discord\FlowMeasure\Message\EcfmpFlowMeasureMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Provider\PendingEcfmpMessage;
use App\Discord\FlowMeasure\Sender\EcfmpFlowMeasureSender;
use App\Enums\DiscordNotificationType;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType as DiscordNotificationTypeModel;
use App\Models\FlowMeasure;
use Config;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class EcfmpFlowMeasureSenderTest extends TestCase
{
    private readonly MockInterface|DiscordServiceInterface $discordService;
    private readonly MockInterface|FlowMeasureMessageFactory $messageFactory;
    private readonly EcfmpFlowMeasureSender $sender;

    public function setUp(): void
    {
        parent::setUp();
        $this->discordService = Mockery::mock(DiscordServiceInterface::class);
        $this->messageFactory = Mockery::mock(FlowMeasureMessageFactory::class);
        $this->app->instance(DiscordServiceInterface::class, $this->discordService);
        $this->app->instance(FlowMeasureMessageFactory::class, $this->messageFactory);

        Config::set('discord.client_request_app_id', 'testabc');
        $this->sender = $this->app->make(EcfmpFlowMeasureSender::class);
    }

    public function testItSendsTheMessage(): void
    {
        $flowMeasure = FlowMeasure::factory()->create();
        $pendingMessage = new PendingEcfmpMessage($flowMeasure, DiscordNotificationType::FLOW_MEASURE_ACTIVATED, Mockery::mock(NotificationReissuerInterface::class));
        $flowMeasureMessage = new EcfmpFlowMeasureMessage('test-abc', Mockery::mock(FlowMeasureRecipientsInterface::class), Mockery::mock(FlowMeasureEmbedInterface::class));

        $this->messageFactory->shouldReceive('makeEcfmp')->once()->with($pendingMessage)->andReturn($flowMeasureMessage);

        $expectedClientRequestId = 'testabc-' . DiscordNotificationType::FLOW_MEASURE_ACTIVATED->value . '-' . $flowMeasure->id . '-' . $flowMeasure->identifier;

        $this->discordService->shouldReceive('sendMessage')->once()->with($expectedClientRequestId, $flowMeasureMessage)->andReturn('1234567890');

        $this->sender->send($pendingMessage);

        $notification = DiscordNotification::latest()->first();
        $this->assertEquals('1234567890', $notification->remote_id);

        $this->assertDatabaseHas('discord_notification_flow_measure', [
            'discord_notification_type_id' => DiscordNotificationTypeModel::idFromEnum(DiscordNotificationType::FLOW_MEASURE_ACTIVATED),
            'notified_as' => $flowMeasure->identifier,
            'discord_notification_id' => $notification->id,
            'flow_measure_id' => $flowMeasure->id,
        ]);
    }

    public function testItHandlesExceptionIfSendingFails(): void
    {
        $startNotificationCount = DiscordNotification::count();

        $flowMeasure = FlowMeasure::factory()->create();
        $pendingMessage = new PendingEcfmpMessage($flowMeasure, DiscordNotificationType::FLOW_MEASURE_ACTIVATED, Mockery::mock(NotificationReissuerInterface::class));
        $flowMeasureMessage = new EcfmpFlowMeasureMessage('test-abc', Mockery::mock(FlowMeasureRecipientsInterface::class), Mockery::mock(FlowMeasureEmbedInterface::class));

        $this->messageFactory->shouldReceive('makeEcfmp')->once()->with($pendingMessage)->andReturn($flowMeasureMessage);

        $expectedClientRequestId = 'testabc-' . DiscordNotificationType::FLOW_MEASURE_ACTIVATED->value . '-' . $flowMeasure->id . '-' . $flowMeasure->identifier;

        $this->discordService->shouldReceive('sendMessage')->once()->with($expectedClientRequestId, $flowMeasureMessage)->andThrow(new DiscordServiceException('test'));

        $this->sender->send($pendingMessage);


        $this->assertDatabaseCount('discord_notifications', $startNotificationCount);
        $this->assertDatabaseMissing('discord_notification_flow_measure', [
            'flow_measure_id' => $flowMeasure->id,
        ]);
    }
}
