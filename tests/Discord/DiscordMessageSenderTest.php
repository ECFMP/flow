<?php

namespace Tests\Discord;

use App\Discord\DiscordMessageSender;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Embed\TitleInterface;
use App\Discord\Message\MessageInterface;
use App\Discord\Webhook\WebhookInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class DiscordMessageSenderTest extends TestCase
{
    private readonly DiscordMessageSender $sender;

    public function setUp(): void
    {
        parent::setUp();
        $this->sender = $this->app->make(DiscordMessageSender::class);

        Config::set('discord.enabled', false);
        Config::set('discord.avatar_url', 'http://ecfmp.dev/images/avatar.png');
        Config::set('discord.token', 'abc');
        Config::set('discord.username', 'FlowBot');
    }

    public function getMessage(): MessageInterface
    {
        return new class implements MessageInterface {
            public function content(): string
            {
                return 'ohai';
            }

            public function embeds(): EmbedCollection
            {
                $mockTitle = Mockery::mock(TitleInterface::class);
                $mockTitle->shouldReceive('title')->once()->andReturn('Hai');

                return (new EmbedCollection())->add(Embed::make()->withTitle($mockTitle));
            }

            public function destination(): WebhookInterface
            {
                return tap(
                    Mockery::mock(WebhookInterface::class),
                    function (Mockery\MockInterface $webhook) {
                        $webhook->shouldReceive('url')->andReturn('https://vatsim.net');
                    }
                );
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
            fn(Request $request) => $request->url() === 'https://vatsim.net' &&
                $request->method() === 'POST' &&
                $request->isJson() &&
                json_decode($request->body(), true) === [
                    'username' => 'FlowBot',
                    'avatar_url' => 'http://ecfmp.dev/images/avatar.png',
                    'content' => 'ohai',
                    'embeds' => [
                        [
                            'title' => 'Hai',
                        ],
                    ],
                    'tts' => false,
                    'allowed_mentions' => [
                        'parse' => [
                            'users',
                            'roles',
                        ],
                    ],
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
            fn(Request $request) => $request->url() === 'https://vatsim.net' &&
                $request->method() === 'POST' &&
                $request->isJson() &&
                json_decode($request->body(), true) === [
                    'username' => 'FlowBot',
                    'avatar_url' => 'http://ecfmp.dev/images/avatar.png',
                    'content' => 'ohai',
                    'embeds' => [
                        [
                            'title' => 'Hai',
                        ],
                    ],
                    'tts' => false,
                    'allowed_mentions' => [
                        'parse' => [
                            'users',
                            'roles',
                        ],
                    ],
                ]
        );
    }
}
