<?php

namespace Tests\Jobs;

use App\Discord\Message\Sender\DivisionWebhookSender;
use App\Jobs\SendDiscordNotifications;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class SendDiscordNotificationsTest extends TestCase
{
    public function testItRunsNotificationSending()
    {
        Config::set('discord.enabled', true);

        $senderMock = Mockery::mock(DivisionWebhookSender::class);
        $senderMock->shouldReceive('sendDiscordMessages')->once();
        $this->app->instance(DivisionWebhookSender::class, $senderMock);

        $job = $this->app->make(SendDiscordNotifications::class);
        $job->handle();
    }

    public function testItDoesntRunsNotificationSendingIfSwitchedOff()
    {
        Config::set('discord.enabled', false);

        $senderMock = Mockery::mock(DivisionWebhookSender::class);
        $senderMock->shouldReceive('sendDiscordMessages')->never();
        $this->app->instance(DivisionWebhookSender::class, $senderMock);

        $job = $this->app->make(SendDiscordNotifications::class);
        $job->handle();
    }
}
