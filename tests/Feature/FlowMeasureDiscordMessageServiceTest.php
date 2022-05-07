<?php

namespace Tests\Feature;

use App\Discord\DiscordInterface;
use App\Discord\Message\MessageInterface;
use App\Enums\DiscordNotificationType;
use App\Models\DiscordNotification;
use App\Models\FlowMeasure;
use App\Service\FlowMeasureDiscordMessageService;
use Config;
use DB;
use Mockery;
use Mockery\MockInterface;
use Str;
use Tests\TestCase;

class FlowMeasureDiscordMessageServiceTest extends TestCase
{
    private readonly MockInterface $discord;
    private readonly FlowMeasureDiscordMessageService $service;

    public function setUp(): void
    {
        parent::setUp();
        DB::table('discord_notifications')->delete();
        DB::table('flow_measures')->delete();
        $this->discord = Mockery::mock(DiscordInterface::class);
        $this->app->instance(DiscordInterface::class, $this->discord);
        $this->service = $this->app->make(FlowMeasureDiscordMessageService::class);
        Config::set('discord.enabled', true);
    }

    public function testItSendsNotificationForActiveFlowMeasures()
    {
        $measure1 = FlowMeasure::factory()->create();
        $measure2 = FlowMeasure::factory()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(MessageInterface $message) => Str::contains(
                        $message->content(),
                        'Flow Measure Activated'
                    ) && Str::contains($message->content(), $measure1->identifier)
            )
        )
            ->once();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(MessageInterface $message) => Str::contains(
                        $message->content(),
                        'Flow Measure Activated'
                    ) && Str::contains($message->content(), $measure2->identifier)
            )
        )
            ->once();

        $this->service->sendDiscordNotifications();

        $this->assertDatabaseHas(
            'discord_notifications',
            [
                'flow_measure_id' => $measure1->id,
                'type' => DiscordNotificationType::FLOW_MEASURE_ACTIVATED->value,
            ]
        );

        $this->assertDatabaseHas(
            'discord_notifications',
            [
                'flow_measure_id' => $measure2->id,
                'type' => DiscordNotificationType::FLOW_MEASURE_ACTIVATED->value,
            ]
        );
    }

    public function testItDoesntSendNotificationFlowMeasureNotStarted()
    {
        FlowMeasure::factory()->notStarted()->create();
        $this->discord->expects('sendMessage')->never();
        $this->service->sendDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotificationFlowMeasureFinished()
    {
        FlowMeasure::factory()->finished()->create();
        $this->discord->expects('sendMessage')->never();
        $this->service->sendDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotificationFlowMeasureDeleted()
    {
        FlowMeasure::factory()->create()->delete();
        $this->discord->expects('sendMessage')->never();
        $this->service->sendDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotificationIfAlreadySent()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'flow_measure_id' => $flowMeasure->id,
                    'type' => DiscordNotificationType::FLOW_MEASURE_ACTIVATED,
                    'content' => 'abc',
                ]
            );
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }
}
