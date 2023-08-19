<?php

namespace Tests\Api;

use App\Http\Resources\EventResource;
use App\Http\Resources\FlightInformationRegionResource;
use App\Http\Resources\FlowMeasureResource;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PluginTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::forget('plugin_api_response');
    }

    public function tearDown(): void
    {
        Cache::forget('plugin_api_response');
        parent::tearDown();
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

    public function testItReturnsCachedData()
    {
        // Put some fake data in cache
        Cache::remember(
            'plugin_api_response',
            Carbon::now()->addMinutes(1),
            fn () => [
                'events' => [],
                'flight_information_regions' => [],
                'flow_measures' => [],
            ]
        );

        FlightInformationRegion::factory()->count(5)->create();
        Event::factory()->count(3)->create();

        // Should show, if the data weren't cached
        $active = FlowMeasure::factory()
            ->create();

        $notStarted = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $finished = FlowMeasure::factory()
            ->finished()
            ->create();

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

        // Should show
        $active = FlowMeasure::factory()
            ->create();

        $notStarted = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $finished = FlowMeasure::factory()
            ->finished()
            ->create();

        // Shouldn't show, deleted
        $deleted = FlowMeasure::factory()
            ->create();
        $deleted->delete();

        // Shouldn't show, too far in the future
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->withEvent()
            ->create();

        // Shouldn't show, too far in the past
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->withEvent()
            ->create();

        $this->get('api/v1/plugin')
            ->assertStatus(200)
            ->assertExactJson(
                [
                    'events' => EventResource::collection(Event::all())->toArray(new Request()),
                    'flight_information_regions' => FlightInformationRegionResource::collection(
                        FlightInformationRegion::all()
                    )->toArray(new Request()),
                    'flow_measures' => FlowMeasureResource::collection(
                        FlowMeasure::whereIn('id', [$active->id, $notStarted->id, $finished->id])->get()
                    )->toArray(new Request()),
                ]
            );
    }

    public function testItReturnsPluginApiDataWithDeleted()
    {
        FlightInformationRegion::factory()->count(5)->create();
        Event::factory()->count(3)->create();

        // Should show
        $active = FlowMeasure::factory()
            ->create();

        $notStarted = FlowMeasure::factory()
            ->notStarted()
            ->create();

        $finished = FlowMeasure::factory()
            ->finished()
            ->create();

        // Should show, deleted
        $deleted = FlowMeasure::factory()
            ->create();
        $deleted->delete();

        // Shouldn't show, too far in the future
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->addDay()->addHour(), Carbon::now()->addDay()->addHours(2))
            ->withEvent()
            ->create();

        // Shouldn't show, too far in the past
        FlowMeasure::factory()
            ->withTimes(Carbon::now()->subDay()->subHours(3), Carbon::now()->subDay()->subHours(2))
            ->withEvent()
            ->create();

        $this->get('api/v1/plugin?deleted=1')
            ->assertStatus(200)
            ->assertExactJson(
                [
                    'events' => EventResource::collection(Event::all())->toArray(new Request()),
                    'flight_information_regions' => FlightInformationRegionResource::collection(
                        FlightInformationRegion::all()
                    )->toArray(new Request()),
                    'flow_measures' => FlowMeasureResource::collection(
                        FlowMeasure::whereIn('id', [$active->id, $notStarted->id, $finished->id, $deleted->id])->withTrashed()->get()
                    )->toArray(new Request()),
                ]
            );
    }
}
