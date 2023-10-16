<?php

namespace App\Discord\FlowMeasure\Sender;

use App\Discord\DiscordServiceInterface;
use App\Discord\Exception\DiscordServiceException;
use App\Discord\FlowMeasure\Message\FlowMeasureMessageFactory;
use App\Discord\FlowMeasure\Provider\PendingMessageInterface;
use App\Models\DiscordNotification;
use App\Models\DiscordNotificationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EcfmpFlowMeasureSender
{
    private readonly DiscordServiceInterface $discordService;

    private readonly FlowMeasureMessageFactory $messageFactory;

    public function __construct(DiscordServiceInterface $discordService, FlowMeasureMessageFactory $messageFactory)
    {
        $this->discordService = $discordService;
        $this->messageFactory = $messageFactory;

        if (!config('discord.client_request_app_id')) {
            throw new RuntimeException('Discord client request app id is not set');
        }
    }

    public function send(PendingMessageInterface $message): void
    {
        // Make the message
        $discordMessage = $this->messageFactory->makeEcfmp($message);

        // Send the message
        try {
            $discordNotificationId = $this->discordService->sendMessage($this->makeClientRequestId($message), $discordMessage);
        } catch (DiscordServiceException $e) {
            Log::error('Failed to send Discord message for flow measure');
            return;
        }

        // Commit the notification to the database
        DB::transaction(function () use ($message, $discordNotificationId) {
            $notification = DiscordNotification::create([
                'remote_id' => $discordNotificationId,
            ]);
            $message->flowMeasure()->discordNotifications()->attach($notification, [
                'discord_notification_type_id' => DiscordNotificationType::idFromEnum($message->type()),
                'notified_as' => $message->flowMeasure()->identifier,
            ]);
        });
    }

    private function makeClientRequestId(PendingMessageInterface $message): string
    {
        return sprintf(
            '%s-%s-%d-%s',
            config('discord.client_request_app_id'),
            $message->type()->value,
            $message->flowMeasure()->id,
            $message->flowMeasure()->identifier
        );
    }
}
