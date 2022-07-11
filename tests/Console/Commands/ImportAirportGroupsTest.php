<?php

namespace Tests\Console\Commands;

use App\Models\Airport;
use App\Models\AirportGroup;
use Artisan;
use Exception;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class ImportAirportGroupsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Airport::create(['icao_code' => 'EGBB']);
        Storage::fake('imports');
        Storage::disk('imports')->put(
            'test.csv',
            'Test Group,"EGBB, EGXX"'
        );
    }

    public function testItThrowsExceptionIfNoFilenameProvided()
    {
        $this->expectException(Exception::class);
        Artisan::call('airports:import-groups');
    }

    public function testItThrowsExceptionIfFileDoesntExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Airport group file not found: test2.csv');
        Artisan::call('airports:import-groups test2.csv');
    }

    public function testItImportsAirportGroups()
    {
        Artisan::call('airports:import-groups test.csv');

        $this->assertDatabaseCount('airport_groups', 1);
        $this->assertDatabaseHas(
            'airport_groups',
            [
                'name' => 'Test Group',
            ]
        );
        $this->assertDatabaseHas(
            'airport_airport_group',
            [
                'airport_group_id' => AirportGroup::where('name', 'Test Group')->firstOrFail()->id,
                'airport_id' => Airport::where('icao_code', 'EGBB')->firstOrFail()->id
            ]
        );
    }
}
