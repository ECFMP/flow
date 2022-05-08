<?php

namespace App\Console\Commands;

use App\Service\FlowMeasureDiscordMessageService;
use Illuminate\Console\Command;

class SendDiscordNotifications extends Command
{
    public const COMMAND_SIGNATURE = 'discord:send-notifications';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::COMMAND_SIGNATURE;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send discord notifications about active flow measures';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FlowMeasureDiscordMessageService $discordMessageService)
    {
        if (!config('discord.enabled')) {
            $this->info('Skipping discord notifications');
            return 0;
        }

        $this->info('Sending discord notifications');
        $discordMessageService->sendMeasureActivatedDiscordNotifications();
        $discordMessageService->sendMeasureWithdrawnDiscordNotifications();
        $discordMessageService->sendMeasureExpiredDiscordNotifications();
        $this->info('Discord notification sending complete');

        return 0;
    }
}
