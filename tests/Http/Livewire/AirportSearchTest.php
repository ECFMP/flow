<?php

use App\Http\Livewire\AirportSearch;
use App\Models\Airport;

use function Pest\Livewire\livewire;

it('can render', function () {
    livewire(AirportSearch::class)
        ->assertOk();
});

it('displays filtered airport options', function () {
    $airport1 = Airport::factory()->create();
    $airport2 = Airport::factory()->create();

    livewire(AirportSearch::class)
        ->set('query', $airport1->icao_code)
        ->assertSee($airport1->icao_code)
        ->assertDontSee($airport2->icao_code);
});

it('displays partially matched airport options', function () {
    Airport::factory()->create(['icao_code' => 'EGCC']);
    Airport::factory()->create(['icao_code' => 'EGKR']);
    Airport::factory()->create(['icao_code' => 'EGKK']);
    Airport::factory()->create(['icao_code' => 'LIRF']);

    livewire(AirportSearch::class)
        ->set('query', 'EGK')
        ->assertSee('EGKK')
        ->assertSee('EGKR')
        ->assertDontSee('EGCC')
        ->assertDontSee('LIRF');
});

it('airports can be selected by mouse over and click', function () {
    Airport::factory()->create(['icao_code' => 'EGCC']);
    $airport = Airport::factory()->create(['icao_code' => 'EGKR']);
    Airport::factory()->create(['icao_code' => 'EGKK']);
    Airport::factory()->create(['icao_code' => 'LIRF']);

    livewire(AirportSearch::class)
        ->set('query', 'E')
        ->call('setHighlight', 2)
        ->call('selectAirport')
        ->assertSet('selectedAirport.id', $airport->id)
        ->assertSet('query', '')
        ->assertSet('airports', [])
        ->assertSet('highlightIndex', 0)
        ->assertEmitted('airportIdUpdated', $airport->id);
});

it('airports can be selected arrow keys', function () {
    $airport = Airport::factory()->create(['icao_code' => 'EGCC']);
    Airport::factory()->create(['icao_code' => 'EGKR']);
    Airport::factory()->create(['icao_code' => 'EGKK']);
    Airport::factory()->create(['icao_code' => 'LIRF']);

    livewire(AirportSearch::class)
        ->set('query', 'E')
        ->call('incrementHighlight')
        ->call('incrementHighlight')
        ->call('decrementHighlight')
        ->call('decrementHighlight')
        ->call('selectAirport')
        ->assertSet('selectedAirport.id', $airport->id)
        ->assertSet('query', '')
        ->assertSet('airports', [])
        ->assertSet('highlightIndex', 0)
        ->assertEmitted('airportIdUpdated', $airport->id);
});
