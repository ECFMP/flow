<?php

namespace Tests\Imports;

use App\Imports\AirportImport;
use App\Models\Airport;
use Illuminate\Console\OutputStyle;
use Mockery;
use Tests\TestCase;

class AirportImportTest extends TestCase
{
    private AirportImport $importer;

    public function setUp(): void
    {
        parent::setUp();
        $this->importer = $this->app->make(AirportImport::class);
        $this->importer->withOutput(Mockery::spy(OutputStyle::class));
    }

    public function testItIsUniqueByIcaoCode()
    {
        $this->assertEquals('icao_code', $this->importer->uniqueBy());
    }

    public function testItHasABatchSize()
    {
        $this->assertEquals(500, $this->importer->batchSize());
    }

    public function testItReturnsAModelForAnAirport()
    {
        $this->assertEquals(new Airport(['icao_code' => 'EGLL']), $this->importer->model(['EGLL']));
    }

    public function testItReturnsNullIfTheRowHasTooManyItems()
    {
        $this->assertNull($this->importer->model(['EGLL', 'EGKK']));
    }

    public function testItReturnsNullIfTheRowHasTooFewItems()
    {
        $this->assertNull($this->importer->model([]));
    }

    public function testItReturnsNullIfTheItemIsNotString()
    {
        $this->assertNull($this->importer->model([123]));
    }

    public function testItReturnsNullIfTheItemIsTooShort()
    {
        $this->assertNull($this->importer->model(['EGL']));
    }

    public function testItReturnsNullIfTheItemIsTooLong()
    {
        $this->assertNull($this->importer->model(['EGLLL']));
    }
}
