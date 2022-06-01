<?php

namespace Tests\Console\Commands;

use App\Models\Airport;
use Artisan;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class ImportAirportsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Airport::create(['icao_code' => 'EGBB']);
        Storage::fake('imports');
        Storage::disk('imports')->put(
            'test.csv',
            Arr::join(['EGKK', 'EGLL', 'EGSS', 'EGKK', 'EGBB'], "\n")
        );
    }

    public function testItThrowsExceptionIfNoFilenameProvided()
    {
        $this->expectException(Exception::class);
        Artisan::call('airports:import');
    }

    public function testItThrowsExceptionIfFileDoesntExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Airport file not found: test2.csv');
        Artisan::call('airports:import test2.csv');
    }

    public function testItImportsAirports()
    {
        Artisan::call('airports:import test.csv');
        $airports = Airport::all()->pluck('icao_code')->toArray();

        $this->assertCount(4, $airports);
        $this->assertEquals(['EGBB', 'EGKK', 'EGLL', 'EGSS'], $airports);
    }
}
