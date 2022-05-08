<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class OptimiseTables extends Command
{
    private const TABLES_TO_OPTIMISE = [
        'events',
        'flow_measures',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:optimise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run optimise on the database tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Optimising database tables');
        foreach (self::TABLES_TO_OPTIMISE as $table) {
            $this->info(sprintf('Optimising table %s', $table));
            DB::statement(sprintf('OPTIMIZE TABLE %s', $table));
        }

        $this->info('Table optimisation complete');
        return 0;
    }
}
