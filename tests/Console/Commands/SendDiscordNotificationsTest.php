<?php

namespace Tests\Console\Commands;

use App\Discord\Message\Sender\Sender;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class SendDiscordNotificationsTest extends TestCase
{
    public function testItRunsNotificationSending()
    {
        Config::set('discord.enabled', true);

        $senderMock = Mockery::mock(Sender::class);
        $senderMock->shouldReceive('sendDiscordMessages')->once();
        $this->app->instance(Sender::class, $senderMock);

        Artisan::call('discord:send-notifications');
    }

    public function testItDoesntRunsNotificationSendingIfSwitchedOff()
    {
        Config::set('discord.enabled', false);

        $senderMock = Mockery::mock(Sender::class);
        $senderMock->shouldReceive('sendDiscordMessages')->never();
        $this->app->instance(Sender::class, $senderMock);

        Artisan::call('discord:send-notifications');
    }
}
