<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldData extends Command
{
    private const MONTHS_TO_KEEP = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes any events or flow measures older than ' . self::MONTHS_TO_KEEP . ' months';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        FlowMeasure::withTrashed()
            ->where('created_at', '<', Carbon::now()->subMonths(self::MONTHS_TO_KEEP))
            ->forceDelete();

        Event::withTrashed()
            ->where('created_at', '<', Carbon::now()->subMonths(self::MONTHS_TO_KEEP))
            ->forceDelete();
        return 0;
    }
}
