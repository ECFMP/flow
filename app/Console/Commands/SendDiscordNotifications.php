<?php

namespace App\Console\Commands;

use App\Discord\Message\Sender\Sender;
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
    protected $description = 'Send discord notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Sender $sender)
    {
        if (!config('discord.enabled')) {
            $this->info('Skipping discord notifications');
            return 0;
        }

        $this->info('Sending discord notifications');
        $sender->sendDiscordMessages();
        $this->info('Discord notification sending complete');

        return 0;
    }
}
