<?php

namespace Tests\Api;

use App\Http\Resources\EventResource;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PluginTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
        DB::table('users')->delete();
        DB::table('events')->delete();
        DB::table('flight_information_regions')->delete();
    }

    public function testItReturnsEmptyPluginApiData()
    {
        $this->get('api/v1/plugin')
            ->assertStatus(200)
            ->assertExactJson(
                [
                    'events' => [],
                    'flight_information_regions' => [],
                    'flow_measures' => [],
                ]
            );
    }

    public function testItReturnsPluginApiData()
    {
        FlightInformationRegion::factory()->count(5)->create();
        Event::factory()->count(3)->create();
        FlowMeasure::factory()->count(2)->create();

        $this->get('api/v1/plugin')
            ->assertStatus(200)
            ->assertExactJson(
                [
                    'events' => EventResource::collection(Event::all())->toArray(new Request()),
                    'flight_information_regions' => FlightInformationRegionResource::collection(
                        FlightInformationRegion::all()
                    )->toArray(new Request()),
                    'flow_measures' => FlowMeasureResource::collection(FlowMeasure::all())->toArray(new Request()),
                ]
            );
    }
}
