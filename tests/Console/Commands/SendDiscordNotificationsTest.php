<?php

namespace Tests\Console\Commands;

use App\Service\FlowMeasureDiscordMessageService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class SendDiscordNotificationsTest extends TestCase
{
    public function testItRunsNotificationSending()
    {
        Config::set('discord.enabled', true);

        $serviceMock = Mockery::mock(FlowMeasureDiscordMessageService::class);
        $serviceMock->shouldReceive('sendMeasureNotifiedDiscordNotifications')->once();
        $serviceMock->shouldReceive('sendMeasureActivatedDiscordNotifications')->once();
        $serviceMock->shouldReceive('sendMeasureWithdrawnDiscordNotifications')->once();
        $serviceMock->shouldReceive('sendMeasureExpiredDiscordNotifications')->once();
        $this->app->instance(FlowMeasureDiscordMessageService::class, $serviceMock);

        Artisan::call('discord:send-notifications');
    }

    public function testItDoesntRunsNotificationSendingIfSwitchedOff()
    {
        Config::set('discord.enabled', false);

        $serviceMock = Mockery::mock(FlowMeasureDiscordMessageService::class);
        $serviceMock->shouldNotReceive('sendMeasureNotifiedDiscordNotifications');
        $serviceMock->shouldNotReceive('sendMeasureActivatedDiscordNotifications');
        $serviceMock->shouldNotReceive('sendMeasureWithdrawnDiscordNotifications');
        $serviceMock->shouldNotReceive('sendMeasureExpiredDiscordNotifications');
        $this->app->instance(FlowMeasureDiscordMessageService::class, $serviceMock);

        Artisan::call('discord:send-notifications');
    }
}
