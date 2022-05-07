<?php

namespace Tests\Unit;

use App\Service\FlowMeasureDiscordMessageService;
use Config;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;

class SendDiscordNotificationsTest extends TestCase
{
    public function testItRunsNotificationSending()
    {
        Config::set('discord.enable', true);

        $serviceMock = Mockery::mock(FlowMeasureDiscordMessageService::class);
        $serviceMock->shouldReceive('sendMeasureActivatedDiscordNotifications')->once();
        $this->app->instance(FlowMeasureDiscordMessageService::class, $serviceMock);

        Artisan::call('discord:send-notifications');
    }

    public function testItDoesntRunsNotificationSendingIfSwitchedOff()
    {
        Config::set('discord.enable', false);

        $serviceMock = Mockery::mock(FlowMeasureDiscordMessageService::class);
        $serviceMock->shouldNotReceive('sendMeasureActivatedDiscordNotifications');
        $this->app->instance(FlowMeasureDiscordMessageService::class, $serviceMock);

        Artisan::call('discord:send-notifications');
    }
}
