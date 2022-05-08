<?php

namespace App\Discord;

use App\Discord\Message\MessageInterface;
use Illuminate\Support\Facades\Http;
use Log;

class DiscordMessageSender implements DiscordInterface
{
    public function sendMessage(MessageInterface $message): bool
    {
        if (!$this->messageSendingEnabled()) {
            Log::info('Skipping discord message as disabled.');
            return false;
        }

        $response = Http::post(
            config('discord.webhook_url'),
            [
                'content' => $message->content(),
                'tts' => false,
                'embeds' => [],
            ]
        );

        if (!$response->successful()) {
            Log::error(
                sprintf(
                    'Discord message sending failed with status %d, response: %s',
                    $response->status(),
                    $response->body()
                )
            );
            return false;
        }

        return true;
    }

    private function messageSendingEnabled(): bool
    {
        return config('discord.enabled', false) === true;
    }
}