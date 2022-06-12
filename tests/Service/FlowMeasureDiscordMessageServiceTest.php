<?php

namespace Tests\Service;

use App\Discord\DiscordInterface;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureActivatedWithoutRecipientsMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureNotifiedMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureExpiredMessage;
use App\Discord\FlowMeasure\Message\FlowMeasureWithdrawnMessage;
use App\Enums\DiscordNotificationType as DiscordNotificationTypeEnum;
use App\Models\DiscordNotificationType;
use App\Models\FlowMeasure;
use App\Service\FlowMeasureDiscordMessageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
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
                fn(FlowMeasureActivatedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Active'
            )
        )
            ->once();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureActivatedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure2->identifier . ' - ' . 'Active'
            )
        )
            ->once();

        $this->service->sendMeasureActivatedDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 2);
        $this->assertDatabaseCount('discord_notification_flow_measure', 2);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure1->identifier,
            ]
        );

        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure2->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure2->identifier,
            ]
        );
    }

    public function testItLogsActivityForActivatedFlowMeasures()
    {
        $measure1 = FlowMeasure::factory()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureActivatedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Active'
            )
        )
            ->once();

        $this->service->sendMeasureActivatedDiscordNotifications();

        $this->assertDatabaseHas(
            'activity_log',
            [
                'log_name' => 'Discord',
                'description' => 'Sending discord notification',
                'subject_type' => 'App\Models\DiscordNotification',
                'event' => $measure1->identifier . ' - Activated',
            ]
        );
    }

    public function testItDoesntSendNotificationFlowMeasureNotStarted()
    {
        FlowMeasure::factory()->notStarted()->create();
        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotificationFlowMeasureFinished()
    {
        FlowMeasure::factory()->finished()->create();
        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotificationFlowMeasureDeleted()
    {
        FlowMeasure::factory()->create()->delete();
        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotificationIfAlreadySent()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }

    public function testItSendsNotificationForActiveFlowMeasuresIfIdentifierHasChangedSinceNotification()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->identifier = $flowMeasure->identifier . '-2';
            $flowMeasure->save();
        })->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureActivatedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Active (Reissued)'
            )
        )
            ->once();

        $this->service->sendMeasureActivatedDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 2);
        $this->assertDatabaseCount('discord_notification_flow_measure', 2);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure1->identifier,
            ]
        );
    }

    public function testItSendsActivationWithInterestedPartiesIfReissuedSinceNotification()
    {
        $measure = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->identifier = $flowMeasure->identifier . '-2';
            $flowMeasure->save();
        })->create();

        $notification = $measure->discordNotifications->first();
        $notification->created_at = Carbon::now()->subMinutes(30);
        $notification->save();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureActivatedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure->identifier . ' - ' . 'Active (Reissued)'
            )
        )
            ->once();

        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure->identifier,
            ]
        );
        $this->assertDatabaseCount('discord_notifications', 2);
    }

    public function testItSendsActivationWithInterestedPartiesIfNotifiedMessageSentOverAnHourAgo()
    {
        $measure = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->create();

        $notification = $measure->discordNotifications->first();
        $notification->created_at = Carbon::now()->subHours(3);
        $notification->save();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureActivatedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure->identifier . ' - ' . 'Active'
            )
        )
            ->once();

        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure->identifier,
            ]
        );
        $this->assertDatabaseCount('discord_notifications', 2);
    }

    public function testItSendsActivationWithoutInterestedPartiesIfNotifiedMessageSentLessThanOneHourAgo()
    {
        $measure = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->create();

        $notification = $measure->discordNotifications->first();
        $notification->created_at = Carbon::now()->subMinutes(30);
        $notification->save();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureActivatedWithoutRecipientsMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure->identifier . ' - ' . 'Active'
            )
        )
            ->once();

        $this->service->sendMeasureActivatedDiscordNotifications();
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                ),
                'notified_as' => $measure->identifier,
            ]
        );
        $this->assertDatabaseCount('discord_notifications', 2);
    }

    public function testItSendsWithdrawnNotifications()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->create();
        $measure2 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureWithdrawnMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Withdrawn'
            )
        )
            ->once();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureWithdrawnMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure2->identifier . ' - ' . 'Withdrawn'
            )
        )
            ->once();

        $this->service->sendMeasureWithdrawnDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 4);
        $this->assertDatabaseCount('discord_notification_flow_measure', 4);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                ),
                'notified_as' => $measure1->identifier,
            ]
        );

        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure2->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                ),
                'notified_as' => $measure2->identifier,
            ]
        );
    }

    public function testItLogsActivityForWithdrawnFlowMeasures()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureWithdrawnMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Withdrawn'
            )
        )
            ->once();

        $this->service->sendMeasureWithdrawnDiscordNotifications();

        $this->assertDatabaseHas(
            'activity_log',
            [
                'log_name' => 'Discord',
                'description' => 'Sending discord notification',
                'subject_type' => 'App\Models\DiscordNotification',
                'event' => $measure1->identifier . ' - Withdrawn',
            ]
        );
    }

    public function testItDoesntSendWithdrawnNotificationsIfNotDeleted()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureWithdrawnDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }

    public function testItDoesntSendWithdrawnNotificationsIfFlowMeasureNotYetActive()
    {
        FlowMeasure::factory()->notStarted()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureWithdrawnDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }

    public function testItDoesntSendWithdrawnNotificationsIfActivatedMessageNotSent()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->delete();
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureWithdrawnDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendWithdrawnNotificationsIfAlreadySend()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureWithdrawnDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 2);
    }

    public function testItSendsExpiredNotifications()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->finished()->create();
        $measure2 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->finished()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureExpiredMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Expired'
            )
        )
            ->once();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureExpiredMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure2->identifier . ' - ' . 'Expired'
            )
        )
            ->once();

        $this->service->sendMeasureExpiredDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 4);
        $this->assertDatabaseCount('discord_notification_flow_measure', 4);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED
                ),
                'notified_as' => $measure1->identifier,
            ]
        );

        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure2->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED
                ),
                'notified_as' => $measure2->identifier,
            ]
        );
    }

    public function testItLogsActivityForExpiredFlowMeasures()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->finished()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureExpiredMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Expired'
            )
        )
            ->once();

        $this->service->sendMeasureExpiredDiscordNotifications();

        $this->assertDatabaseHas(
            'activity_log',
            [
                'log_name' => 'Discord',
                'description' => 'Sending discord notification',
                'subject_type' => 'App\Models\DiscordNotification',
                'event' => $measure1->identifier . ' - Expired',
            ]
        );
    }

    public function testItSendsWithdrawnMessagesForExpiredFlowMeasures()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->finished()->create();
        $measure2 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->delete();
        })->finished()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureWithdrawnMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Withdrawn'
            )
        )
            ->once();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureWithdrawnMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure2->identifier . ' - ' . 'Withdrawn'
            )
        )
            ->once();

        $this->service->sendMeasureExpiredDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 4);
        $this->assertDatabaseCount('discord_notification_flow_measure', 4);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                ),
                'notified_as' => $measure1->identifier,
            ]
        );

        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure2->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                ),
                'notified_as' => $measure2->identifier,
            ]
        );
    }

    public function testItDoesntSendExpiryIfMeasureNotExpired()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureExpiredDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }

    public function testItDoesntSendExpiryIfActivationMessageNotSent()
    {
        FlowMeasure::factory()->finished()->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureExpiredDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendExpiryIfWithdrawnMessageIsSent()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_WITHDRAWN
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->finished()->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureExpiredDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 2);
    }

    public function testItDoesntSendExpiryIfExpiredMessageIsSent()
    {
        FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_EXPIRED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
        })->finished()->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureExpiredDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 2);
    }

    public function testItSendsNotificationForNotifiedFlowMeasures()
    {
        $measure1 = FlowMeasure::factory()->notStarted()->create();
        $measure2 = FlowMeasure::factory()->notStarted()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureNotifiedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Notified'
            )
        )
            ->once();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureNotifiedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure2->identifier . ' - ' . 'Notified'
            )
        )
            ->once();

        $this->service->sendMeasureNotifiedDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 2);
        $this->assertDatabaseCount('discord_notification_flow_measure', 2);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $measure1->identifier,
            ]
        );

        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure2->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $measure2->identifier,
            ]
        );
    }

    public function testItSendsNotificationForNotifiedFlowMeasuresIfReissued()
    {
        $measure1 = FlowMeasure::factory()->afterCreating(function (FlowMeasure $flowMeasure) {
            $flowMeasure->discordNotifications()->create(
                [
                    'content' => '',
                    'embeds' => [],
                ],
                [
                    'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                        DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                    ),
                    'notified_as' => $flowMeasure->identifier,
                ]
            );
            $flowMeasure->reissueIdentifier();
        })->notStarted()->create();

        $this->discord->expects('sendMessage')->with(
            Mockery::on(
                fn(FlowMeasureNotifiedMessage $message) => $message->embeds()->toArray(
                    )[0]['title'] === $measure1->identifier . ' - ' . 'Notified (Reissued)'
            )
        )
            ->once();

        $this->service->sendMeasureNotifiedDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 2);
        $this->assertDatabaseCount('discord_notification_flow_measure', 2);
        $this->assertDatabaseHas(
            'discord_notification_flow_measure',
            [
                'flow_measure_id' => $measure1->id,
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                    DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                ),
                'notified_as' => $measure1->identifier,
            ]
        );
    }

    public function testItDoesntSendNotifiedMessagesIfTooFarInAdvance()
    {
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->addHours(25), Carbon::now()->addHours(26))
            ->create();
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->addHours(25), Carbon::now()->addHours(26))
            ->create();


        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureNotifiedDiscordNotifications();

        $this->assertDatabaseCount('discord_notifications', 0);
    }

    public function testItDoesntSendNotifiedMessagesIfNotifiedMessageAlreadySent()
    {
        FlowMeasure::factory()
            ->notStarted()
            ->afterCreating(function (FlowMeasure $flowMeasure) {
                $flowMeasure->discordNotifications()->create(
                    [
                        'content' => '',
                        'embeds' => [],
                    ],
                    [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_NOTIFIED
                        ),
                        'notified_as' => $flowMeasure->identifier,
                    ]
                );
            })->create();


        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureNotifiedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }

    public function testItDoesntSendNotifiedMessagesIfActivatedMessageAlreadySent()
    {
        FlowMeasure::factory()
            ->notStarted()
            ->afterCreating(function (FlowMeasure $flowMeasure) {
                $flowMeasure->discordNotifications()->create(
                    [
                        'content' => '',
                        'embeds' => [],
                    ],
                    [
                        'discord_notification_type_id' => DiscordNotificationType::idFromEnum(
                            DiscordNotificationTypeEnum::FLOW_MEASURE_ACTIVATED
                        ),
                        'notified_as' => $flowMeasure->identifier,
                    ]
                );
            })->create();


        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureNotifiedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 1);
    }

    public function testItDoesntSendNotifiedMessagesIfAlreadyActive()
    {
        FlowMeasure::factory()->create();

        $this->discord->expects('sendMessage')->never();
        $this->service->sendMeasureNotifiedDiscordNotifications();
        $this->assertDatabaseCount('discord_notifications', 0);
    }
}
