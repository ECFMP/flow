<?php

namespace App\Discord;

use App\Discord\Client\ClientFactoryInterface;
use App\Discord\Exception\DiscordServiceException;
use App\Discord\Message\Embed\Embed;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\MessageInterface;
use Ecfmp_discord\CreateRequest;
use Ecfmp_discord\CreateResponse;
use Ecfmp_discord\DiscordClient;
use Ecfmp_discord\DiscordEmbeds;
use Grpc\Status;
use Mockery;
use Tests\TestCase;

use const Grpc\STATUS_OK;

class DiscordServiceMessageSenderTest extends TestCase
{
    private readonly ClientFactoryInterface $clientFactory;
    private readonly DiscordClient $client;
    private readonly MessageInterface $message;
    private readonly EmbedCollection $embeds;
    private readonly DiscordEmbeds $discordEmbeds;
    private readonly CreateResponse $response;
    private readonly Status $status;

    private readonly DiscordServiceMessageSender $sender;

    public function setUp(): void
    {
        parent::setUp();

        $this->clientFactory = Mockery::mock(ClientFactoryInterface::class);
        $this->client = Mockery::mock(DiscordClient::class);
        $this->message = Mockery::mock(MessageInterface::class);
        $this->embeds = Mockery::mock(EmbedCollection::class);
        $this->response = Mockery::mock(CreateResponse::class);
        $this->status = Mockery::mock(Status::class);
        $this->sender = new DiscordServiceMessageSender($this->clientFactory);
        $this->discordEmbeds = Mockery::mock(DiscordEmbeds::class);

        $this->clientFactory->shouldReceive('create')->andReturn($this->client);
    }

    public function testItThrowsExceptionIfClientIsNotReady()
    {
        $this->client->shouldReceive('waitForReady')->with(1000000)->andReturn(false);

        $this->expectException(DiscordServiceException::class);
        $this->expectExceptionMessage('Discord grpc channel not ready');

        $this->sender->sendMessage('client-request-id', $this->message);
    }

    public function testItSendsAMessageAndReturnsTheId()
    {
        $this->client->shouldReceive('waitForReady')->with(1000000)->andReturn(true);
        $this->message->shouldReceive('content')->andReturn('content');
        $this->message->shouldReceive('embeds')->andReturn($this->embeds);
        $this->embeds->shouldReceive('toProtobuf')->andReturn([$this->discordEmbeds]);
        $this->client->shouldReceive('Create')->with(Mockery::on(
            fn(CreateRequest $request) => $request->getContent() === 'content'
            // TODO: See if we can check embeds
        ), [
            'authorization' => [config('discord.service_token')],
            'x-client-request-id' => ['client-request-id'],
        ])->andReturn([$this->response, $this->status]);
        $this->status->code = STATUS_OK;

        $this->response->shouldReceive('getId')->andReturn('id');

        $this->assertEquals('id', $this->sender->sendMessage('client-request-id', $this->message));
    }

    public function testItThrowsAnExceptionIfStatusIsNotOk()
    {
        $this->client->shouldReceive('waitForReady')->with(1000000)->andReturn(true);
        $this->message->shouldReceive('content')->andReturn('content');
        $this->message->shouldReceive('embeds')->andReturn($this->embeds);
        $this->embeds->shouldReceive('toProtobuf')->andReturn([$this->discordEmbeds]);
        $this->client->shouldReceive('Create')->with(
            Mockery::on(
                fn(CreateRequest $request, array $metadata) => $request->getContent() === 'content'
                && $request->getEmbeds() === [$this->discordEmbeds]
                && $metadata['authorization'] === [config('discord.service_token')]
                && $metadata['x-client-request-id'] === ['client-request-id']
            )
        )->andReturn([$this->response, $this->status]);
        $this->status->code = 1;
        $this->status->details = 'details';

        $this->expectException(DiscordServiceException::class);
        $this->expectExceptionMessage('Discord grpc call failed');

        $this->sender->sendMessage('client-request-id', $this->message);
    }
}
