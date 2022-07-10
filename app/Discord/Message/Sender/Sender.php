<?php

namespace App\Discord\Message\Sender;

use App\Discord\DiscordInterface;
use App\Models\DiscordNotification;
use DB;
use Exception;

class Sender
{
    private readonly array $generators;
    private readonly DiscordInterface $discord;

    public function __construct(array $generators, DiscordInterface $discord)
    {
        $this->generators = $generators;
        $this->discord = $discord;
    }

    public function sendDiscordMessages(): void
    {
        foreach ($this->generators as $generator) {
            foreach ($generator->generate() as $message) {
                // Send the message
                try {
                    if (!$this->discord->sendMessage($message)) {
                        continue;
                    }
                } catch (Exception) {
                    continue;
                }

                // Associate it and log it
                DB::transaction(function () use ($message) {
                    $notification = DiscordNotification::create(
                        [
                            'division_discord_webhook_id' => $message->destination()->id(),
                            'content' => $message->content(),
                            'embeds' => $message->embeds()->toArray(),
                        ]
                    );
                    $message->associator()->associate($notification);
                    $message->logger()->log($notification);
                });
            }
        }
    }

    public function generators(): array
    {
        return $this->generators;
    }
}
