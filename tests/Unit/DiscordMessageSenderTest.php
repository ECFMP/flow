<?php

namespace Tests\Unit;

use App\Discord\DiscordMessageSender;
use App\Discord\Message\MessageInterface;
use Config;
use Http;
use Illuminate\Http\Client\Request;
use Tests\TestCase;

class DiscordMessageSenderTest extends TestCase
{
    private readonly DiscordMessageSender $sender;

    public function setUp(): void
    {
        parent::setUp();
        $this->sender = $this->app->make(DiscordMessageSender::class);

        Config::set('discord.enabled', false);
        Config::set('discord.webhook_url', 'https://vatsim.net');
        Config::set('discord.token', 'abc');
    }

    public function getMessage(): MessageInterface
    {
        return new class implements MessageInterface {
            public function content(): string
            {
                return 'ohai';
            }
        };
    }

    public function testItDoesntSendIfSendingOff()
    {
        Http::fake();
        $this->assertFalse($this->sender->sendMessage($this->getMessage()));
        Http::assertNothingSent();
    }

    public function testItSendsTheMessage()
    {
        Config::set('discord.enabled', true);
        Http::fake(
            [
                'https://vatsim.net' => Http::response(),
            ]
        );

        $this->assertTrue($this->sender->sendMessage($this->getMessage()));

        Http::assertSentCount(1);
        Http::assertSent(
            fn(Request $request) =>
                $request->url() === 'https://vatsim.net' &&
                $request->method() === 'POST' &&
                $request->isJson() &&
                json_decode($request->body(), true) === [
                    'content' => 'ohai',
                    'tts' => false,
                    'embeds' => [],
                ]
        );
    }

    public function testItFailsToSendTheMessage()
    {
        Config::set('discord.enabled', true);
        Http::fake(
            [
                'https://vatsim.net' => Http::response([], 418),
            ]
        );

        $this->assertFalse($this->sender->sendMessage($this->getMessage()));

        Http::assertSentCount(1);
        Http::assertSent(
            fn(Request $request) =>
                $request->url() === 'https://vatsim.net' &&
                $request->method() === 'POST' &&
                $request->isJson() &&
                json_decode($request->body(), true) === [
                    'content' => 'ohai',
                    'tts' => false,
                    'embeds' => [],
                ]
        );
    }
}
