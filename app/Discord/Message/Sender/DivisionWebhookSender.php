<?php

namespace App\Discord\Message\Sender;

use App\Discord\DiscordWebhookInterface;
use App\Models\DivisionDiscordNotification;
use DB;
use Exception;

class DivisionWebhookSender
{
    private readonly array $generators;
    private readonly DiscordWebhookInterface $discord;

    public function __construct(array $generators, DiscordWebhookInterface $discord)
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
                    $notification = DivisionDiscordNotification::create(
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
