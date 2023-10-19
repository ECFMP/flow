<?php

namespace App\Discord\Client;

use Ecfmp_discord\DiscordClient;
use Grpc\ChannelCredentials;

/**
 * This class exists because gRPC has trouble when the client is registered directly to the
 * service container (the channel is closed before the request is sent). This class is a
 * workaround for that issue.
 * @codeCoverageIgnore
 */
class ClientFactory implements ClientFactoryInterface
{
    private DiscordClient|null $client = null;

    public function create(): DiscordClient
    {
        if ($this->client === null) {
            $this->client = new DiscordClient(
                config('discord.service_host'),
                [
                    'credentials' => ChannelCredentials::createInsecure(),
                    'grpc.primary_user_agent' => config('app.name'),
                ],
            );
        }

        return $this->client;
    }
}
