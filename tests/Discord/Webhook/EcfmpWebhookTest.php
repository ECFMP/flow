<?php

namespace Tests\Discord\Webhook;

use App\Discord\Webhook\EcfmpWebhook;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EcfmpWebhookTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Config::set('discord.webhook_url', 'foo');
    }

    public function testItHasNoId()
    {
        $this->assertNull((new EcfmpWebhook())->id());
    }

    public function testItHasAUrl()
    {
        $this->assertEquals(
            'foo',
            (new EcfmpWebhook())->url()
        );
    }

    public function testItHasADescription()
    {
        $this->assertEquals(
            'ECFMP',
            (new EcfmpWebhook())->description()
        );
    }
}
