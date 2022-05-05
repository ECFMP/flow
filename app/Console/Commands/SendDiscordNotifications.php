<?php

namespace App\Console\Commands;

use App\Service\FlowMeasureDiscordMessageService;
use Illuminate\Console\Command;

class SendDiscordNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:send-notifications';

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
        $this->info('Sending discord notifications');
        $discordMessageService->sendDiscordNotifications();
        $this->info('Discord notification sending complete');

        return 0;
    }
}
