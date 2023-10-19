<?php

namespace App\Discord;

use App\Discord\Client\ClientFactoryInterface;
use App\Discord\Exception\DiscordServiceException;
use App\Discord\Message\EcfmpMessageInterface;
use Ecfmp_discord\CreateRequest;
use Log;

use const Grpc\STATUS_OK;

class DiscordServiceMessageSender implements DiscordServiceInterface
{
    private readonly ClientFactoryInterface $discordClientFactory;

    public function __construct(ClientFactoryInterface $discordClientFactory)
    {
        $this->discordClientFactory = $discordClientFactory;
    }

    public function sendMessage(string $clientRequestId, EcfmpMessageInterface $message): string
    {
        $client = $this->discordClientFactory->create();

        // Wait for 1 second for the channel to be ready
        $channelReady = $client->waitForReady(1000000);
        if (!$channelReady) {
            Log::error('Discord grpc channel not ready');
            throw new DiscordServiceException('Discord grpc channel not ready');
        }

        /**
         * @var $response \Ecfmp_discord\CreateResponse
         */
        [$response, $status] = $client->Create(
            new CreateRequest(
                [
                    'channel' => $message->channel(),
                    'content' => $message->content(),
                    'embeds' => $message->embeds()->toProtobuf(),
                ]
            ),
            [
                'authorization' => [config('discord.service_token')],
                'x-client-request-id' => [$clientRequestId],
            ],
        )->wait();

        if ($status->code !== STATUS_OK) {
            Log::error('Discord grpc call failed', [
                'code' => $status->code,
                'details' => $status->details,
            ]);

            throw new DiscordServiceException('Discord grpc call failed');
        }

        return $response->getId();
    }
}
