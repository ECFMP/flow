<?php

namespace App\Jobs;

use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OptimiseTables implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const TABLES_TO_OPTIMISE = [
        'events',
        'flow_measures',
    ];

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Optimising database tables');
        foreach (self::TABLES_TO_OPTIMISE as $table) {
            Log::info(sprintf('Optimising table %s', $table));
            DB::statement(sprintf('OPTIMIZE TABLE %s', $table));
        }

        Log::info('Table optimisation complete');
    }
}
