<?php

use App\Filament\Pages\AirportTraffic\AirportInbounds;
use App\Models\Airport;
use App\Models\VatsimPilot;
use Illuminate\Support\Carbon;

use function Pest\Livewire\livewire;

it('can display inbound aircraft', function () {
    $airport = Airport::factory()->create();

    $differentDestination = VatsimPilot::factory()
        ->destination('1234')
        ->cruising()
        ->withEstimatedArrivalTime(Carbon::now()->addMinutes(61))
        ->create();

    $landedTooFarInPast = VatsimPilot::factory()
        ->destination($airport)
        ->landedMinutesAgo(6)
        ->create();

    $cruising = VatsimPilot::factory()
        ->destination($airport)
        ->cruising()
        ->withEstimatedArrivalTime(Carbon::now()->addMinutes(33))
        ->create();

    $departing = VatsimPilot::factory()
        ->destination($airport)
        ->departing()
        ->withEstimatedArrivalTime(Carbon::now()->addMinutes(25))
        ->create();

    $descending = VatsimPilot::factory()
        ->destination($airport)
        ->descending()
        ->withEstimatedArrivalTime(Carbon::now()->addMinutes(500))
        ->create();

    $recentlyLanded = VatsimPilot::factory()
        ->destination($airport)
        ->landedMinutesAgo(4)
        ->create();

    livewire(AirportInbounds::class, ['airportId' => $airport->id])
        ->assertCanSeeTableRecords([$recentlyLanded, $descending, $departing, $cruising], inOrder: true)
        ->assertCanNotSeeTableRecords([$landedTooFarInPast, $differentDestination]);
});
