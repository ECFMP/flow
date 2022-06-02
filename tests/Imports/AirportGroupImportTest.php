<?php

namespace Tests\Imports;

use App\Imports\AirportGroupImport;
use App\Models\Airport;
use App\Models\AirportGroup;
use Illuminate\Console\OutputStyle;
use Mockery;
use Tests\TestCase;

class AirportGroupImportTest extends TestCase
{
    private AirportGroupImport $importer;

    public function setUp(): void
    {
        parent::setUp();
        $this->importer = $this->app->make(AirportGroupImport::class);
        $this->importer->withOutput(Mockery::spy(OutputStyle::class));
    }

    public function testItDoesntCreateModelsOnBadData()
    {
        $data = collect(
            [
                collect(
                    [
                        'Test Group',
                    ]
                ),
                collect(
                    [
                        'Test Group',
                        'Test Group',
                        'Test Group',
                    ]
                )
            ]
        );

        $this->importer->collection($data);

        $this->assertDatabaseCount('airport_groups', 0);
    }

    public function testItCreatesGroups()
    {
        $airport1 = Airport::factory()->create();
        $airport2 = Airport::factory()->create();
        $airport3 = Airport::factory()->create();
        $airportX = Airport::factory()->create(['icao_code' => 'XXXX']);
        $airportY = Airport::factory()->create(['icao_code' => 'YYYY']);

        $existing = AirportGroup::factory()->has(Airport::factory()->count(2))->create();
        $data = collect(
            [
                collect(
                    [
                        $existing->name,
                        'XXXX, YYYY',
                    ]
                ),
                collect(
                    [
                        'Test Group 1',
                        $airport1->icao_code . ',' . $airport2->icao_code
                    ]
                ),
                collect(
                    [
                        'Test Group 2',
                        $airport3->icao_code
                    ]
                )
            ]
        );

        $this->importer->collection($data);

        $this->assertDatabaseCount('airport_airport_group', 5);

        // Existing group updates
        $this->assertDatabaseHas(
            'airport_airport_group',
            [
                'airport_group_id' => $existing->id,
                'airport_id' => $airportX->id,
            ]
        );
        $this->assertDatabaseHas(
            'airport_airport_group',
            [
                'airport_group_id' => $existing->id,
                'airport_id' => $airportY->id,
            ]
        );

        // Group 1
        $newGroup1 = AirportGroup::where('name', 'Test Group 1')->firstOrFail();
        $this->assertDatabaseHas(
            'airport_airport_group',
            [
                'airport_group_id' => $newGroup1->id,
                'airport_id' => $airport1->id,
            ]
        );
        $this->assertDatabaseHas(
            'airport_airport_group',
            [
                'airport_group_id' => $newGroup1->id,
                'airport_id' => $airport2->id,
            ]
        );

        // Group 2
        $newGroup2 = AirportGroup::where('name', 'Test Group 2')->firstOrFail();
        $this->assertDatabaseHas(
            'airport_airport_group',
            [
                'airport_group_id' => $newGroup2->id,
                'airport_id' => $airport3->id,
            ]
        );
    }
}
