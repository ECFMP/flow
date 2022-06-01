<?php

namespace App\Console\Commands;

use App\Imports\AirportImport;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
use Storage;

class ImportAirports extends Command
{
    private const DISK_NAME = 'imports';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airports:import {file_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a group of airports from a CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AirportImport $airportImport)
    {
        if (!Storage::disk(self::DISK_NAME)->exists($this->argument('file_name'))) {
            throw new InvalidArgumentException(sprintf('Airport file not found: %s', $this->argument('file_name')));
        }

        $this->output->info('Starting airports import');
        $airportImport->withOutput($this->output)->import($this->argument('file_name'), self::DISK_NAME, Excel::CSV);
        $this->output->info('Finished airports import');
        return 0;
    }
}
