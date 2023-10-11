<?php

namespace Tests\Discord\Message\Sender;

use App\Discord\DiscordWebhookInterface;
use App\Discord\FlowMeasure\Message\MessageGeneratorInterface;
use App\Discord\Message\Associator\AssociatorInterface;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\Logger\LoggerInterface;
use App\Discord\Message\MessageInterface;
use App\Discord\Message\Sender\DivisionWebhookSender;
use App\Models\DivisionDiscordWebhook;
use Mockery;
use Tests\TestCase;

class DivisionWebhookSenderTest extends TestCase
{
    public function testItSendsMessages()
    {
        $divisionWebhook = DivisionDiscordWebhook::factory()->create();

        $associator1 = Mockery::mock(AssociatorInterface::class);
        $associator1->shouldReceive('associate');
        $logger1 = Mockery::mock(LoggerInterface::class);
        $logger1->shouldReceive('log');
        $message1 = Mockery::mock(MessageInterface::class);
        $message1->shouldReceive('destination')->andReturn($divisionWebhook);
        $message1->shouldReceive('content')->andReturn('foo');
        $message1->shouldReceive('embeds')->andReturn(new EmbedCollection());
        $message1->shouldReceive('associator')->andReturn($associator1);
        $message1->shouldReceive('logger')->andReturn($logger1);

        $associator2 = Mockery::mock(AssociatorInterface::class);
        $associator2->shouldReceive('associate');
        $logger2 = Mockery::mock(LoggerInterface::class);
        $logger2->shouldReceive('log');
        $message2 = Mockery::mock(MessageInterface::class);
        $message2->shouldReceive('destination')->andReturn($divisionWebhook);
        $message2->shouldReceive('content')->andReturn('bar');
        $message2->shouldReceive('embeds')->andReturn(new EmbedCollection());
        $message2->shouldReceive('associator')->andReturn($associator2);
        $message2->shouldReceive('logger')->andReturn($logger2);

        $mockGenerator = Mockery::mock(MessageGeneratorInterface::class);
        $mockGenerator->expects('generate')->andReturn(collect([$message1, $message2]));
        $mockDiscord = Mockery::mock(DiscordWebhookInterface::class);
        $mockDiscord->shouldReceive('sendMessage')->with($message1)->andReturnTrue();
        $mockDiscord->shouldReceive('sendMessage')->with($message2)->andReturnTrue();

        $sender = new DivisionWebhookSender([$mockGenerator], $mockDiscord);
        $sender->sendDiscordMessages();

        $this->assertDatabaseHas(
            'division_discord_notifications',
            [
                'division_discord_webhook_id' => $divisionWebhook->id,
                'content' => 'foo',
            ]
        );

        $this->assertDatabaseHas(
            'division_discord_notifications',
            [
                'division_discord_webhook_id' => $divisionWebhook->id,
                'content' => 'bar',
            ]
        );
    }
}
