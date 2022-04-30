<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\AirportGroup;
use App\Models\Event;
use App\Models\FlowMeasure;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Airport::factory()
            ->count(5)
            ->create();

        Airport::factory()
            ->count(3)
            ->has(AirportGroup::factory()->count(1))
            ->create();

        FlowMeasure::factory()
            ->withEvent()
            ->create();

        FlowMeasure::factory()
            ->withMandatoryRoute()
            ->create();

        User::factory()
            ->networkManager()
            ->create();

        Event::factory()
            ->withVatcanCode()
            ->create();
    }
}
