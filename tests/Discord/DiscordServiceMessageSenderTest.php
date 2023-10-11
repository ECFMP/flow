<?php

namespace App\Discord;

use App\Discord\Client\ClientFactoryInterface;
use App\Discord\Exception\DiscordServiceException;
use App\Discord\Message\Embed\EmbedCollection;
use App\Discord\Message\MessageInterface;
use Ecfmp_discord\CreateRequest;
use Ecfmp_discord\CreateResponse;
use Ecfmp_discord\DiscordClient;
use Ecfmp_discord\DiscordEmbeds;
use Grpc\Status;
use Grpc\UnaryCall;
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

    private readonly UnaryCall $unaryCall;

    public function setUp(): void
    {
        parent::setUp();

        $this->clientFactory = Mockery::mock(ClientFactoryInterface::class);
        $this->client = Mockery::mock(DiscordClient::class);
        $this->message = Mockery::mock(MessageInterface::class);
        $this->embeds = Mockery::mock(EmbedCollection::class);
        $this->response = Mockery::mock(CreateResponse::class);
        $this->status = Mockery::mock(Status::class);
        $this->discordEmbeds = Mockery::mock(DiscordEmbeds::class);
        $this->unaryCall = Mockery::mock(UnaryCall::class);

        $this->clientFactory->shouldReceive('create')->andReturn($this->client);
        $this->sender = new DiscordServiceMessageSender($this->clientFactory);
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
        $this->unaryCall->shouldReceive('wait')->andReturn([$this->response, $this->status]);
        $this->client->shouldReceive('Create')->with(Mockery::on(
            fn(CreateRequest $request) => $request->getContent() === 'content' &&
            count($request->getEmbeds()) === 1 &&
            $request->getEmbeds()[0] == $this->discordEmbeds
        ), [
            'authorization' => [config('discord.service_token')],
            'x-client-request-id' => ['client-request-id'],
        ])->andReturn($this->unaryCall);
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
        $this->unaryCall->shouldReceive('wait')->andReturn([$this->response, $this->status]);
        $this->client->shouldReceive('Create')->with(Mockery::on(
            fn(CreateRequest $request) => $request->getContent() === 'content' &&
            count($request->getEmbeds()) === 1 &&
            $request->getEmbeds()[0] == $this->discordEmbeds
        ), [
            'authorization' => [config('discord.service_token')],
            'x-client-request-id' => ['client-request-id'],
        ])->andReturn($this->unaryCall);
        $this->status->code = 1;
        $this->status->details = 'details';

        $this->expectException(DiscordServiceException::class);
        $this->expectExceptionMessage('Discord grpc call failed');

        $this->sender->sendMessage('client-request-id', $this->message);
    }
}
