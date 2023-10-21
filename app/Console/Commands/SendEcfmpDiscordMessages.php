<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Discord\FlowMeasure\Generator\EcfmpFlowMeasureMessageGenerator;

class SendEcfmpDiscordMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:send-ecfmp-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send ECFMP Discord messages';

    public function handle(EcfmpFlowMeasureMessageGenerator $generator): int
    {
        if (!config('discord.enabled')) {
            Log::info('Skipping discord notifications, disabled in config');
            return 0;
        }

        Log::info('Sending discord notifications');
        $generator->generateAndSend();
        Log::info('Discord notification sending complete');

        return 0;
    }
}
