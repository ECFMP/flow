<?php

namespace App\Discord;

use Illuminate\Support\Facades\Http;
use Log;

class DiscordMessageSender implements DiscordInterface
{
    public function sendMessage(MessageInterface $message): void
    {
        $response = Http::post(
            config('discord.webhook_url'),
            [
                'content' => $message->content(),
                'username' => config('discord.username'),
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
        }
    }
}
