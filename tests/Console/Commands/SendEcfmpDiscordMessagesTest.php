<?php

namespace Tests\Console\Commands;

use App\Discord\FlowMeasure\Generator\EcfmpFlowMeasureMessageGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class SendEcfmpDiscordMessagesTest extends TestCase
{
    private readonly EcfmpFlowMeasureMessageGenerator $mockEcfmpFlowMeasureMessageGenerator;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockEcfmpFlowMeasureMessageGenerator = Mockery::mock(EcfmpFlowMeasureMessageGenerator::class);
        $this->app->instance(EcfmpFlowMeasureMessageGenerator::class, $this->mockEcfmpFlowMeasureMessageGenerator);
    }

    public function testItRunsNotificationSending()
    {
        $this->mockEcfmpFlowMeasureMessageGenerator->shouldReceive('generateAndSend')->once();

        Config::set('discord.enabled', true);

        $this->assertEquals(0, Artisan::call('discord:send-ecfmp-messages'));
    }

    public function testItDoesntRunsNotificationSendingIfSwitchedOff()
    {
        $this->mockEcfmpFlowMeasureMessageGenerator->shouldReceive('generateAndSend')->never();

        Config::set('discord.enabled', false);

        $this->assertEquals(0, Artisan::call('discord:send-ecfmp-messages'));
    }
}
