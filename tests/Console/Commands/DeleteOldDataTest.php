<?php

namespace Tests\Console\Commands;

use App\Models\Event;
use App\Models\FlowMeasure;
use Carbon\Carbon;
use DB;
use Tests\TestCase;

class DeleteOldDataTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::table('flow_measures')->delete();
        DB::table('events')->delete();
    }

    public function testItDoesntDeleteValidMeasures()
    {
        $flowMeasure = FlowMeasure::factory()->create();

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('flow_measures', 1);
        $this->assertDatabaseHas(
            'flow_measures',
            [
                'id' => $flowMeasure->id,
            ]
        );
    }

    public function testItDeletesExpiredMeasures()
    {
        FlowMeasure::factory()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->created_at = Carbon::now()->subMonths(3)->subDay();
                $measure->save();
            })
            ->create();

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('flow_measures', 0);
    }

    public function testItDeletesExpiredSoftDeletedMeasures()
    {
        $flowMeasure = FlowMeasure::factory()
            ->afterCreating(function (FlowMeasure $measure) {
                $measure->created_at = Carbon::now()->subMonths(3)->subDay();
                $measure->save();
            })
            ->create();
        $flowMeasure->delete();
        $this->assertDatabaseCount('flow_measures', 1);

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('flow_measures', 0);
    }

    public function testItDoesntDeleteValidEvents()
    {
        $event = Event::factory()->create();

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('events', 1);
        $this->assertDatabaseHas(
            'events',
            [
                'id' => $event->id,
            ]
        );
    }

    public function testItDeletesExpiredEvents()
    {
        Event::factory()
            ->afterCreating(function (Event $event) {
                $event->created_at = Carbon::now()->subMonths(3)->subDay();
                $event->save();
            })
            ->create();

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('events', 0);
    }

    public function testItDeletesExpiredSoftDeletedEvents()
    {
        $event = Event::factory()
            ->afterCreating(function (Event $event) {
                $event->created_at = Carbon::now()->subMonths(3)->subDay();
                $event->save();
            })
            ->create();

        $event->delete();
        $this->assertDatabaseCount('events', 1);

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('events', 0);
    }

    public function testItDeletesFlowMeasuresAssociatedWithExpiredEvents()
    {
        $flowMeasure = FlowMeasure::factory()->withEvent()->create();
        $flowMeasure->event->created_at = Carbon::now()->subMonths(3)->subDay();
        $flowMeasure->event->save();

        $this->artisan('data:delete-old');
        $this->assertDatabaseCount('events', 0);
        $this->assertDatabaseCount('flow_measures', 0);
    }
}
