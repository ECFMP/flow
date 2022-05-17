<?php

use App\Filament\Resources\AirportResource;
use App\Models\Airport;
use App\Models\User;
use Tests\FrontendTestCase;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportResource::getUrl())->assertSuccessful();
});

it('can render edit page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportResource::getUrl('edit', [
        'record' => Airport::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportResource::getUrl('edit', [
        'record' => Airport::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportResource::getUrl('edit', [
        'record' => Airport::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportResource::getUrl('edit', [
        'record' => Airport::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for edit page', function () {
    $this->actingAs(User::factory()->system()->create());
    $airport = Airport::factory()->create();

    livewire(AirportResource\Pages\EditAirport::class, [
        'record' => $airport->getKey(),
    ])
        ->assertSet('data.icao_code', $airport->icao_code);
});

it('can edit', function () {
    $this->actingAs(User::factory()->system()->create());

    $airport = Airport::factory()->create();
    $newData = Airport::factory()->make();

    livewire(AirportResource\Pages\EditAirport::class, [
        'record' => $airport->getKey(),
    ])
        ->set('data.icao_code', $newData->icao_code)
        ->call('save');

    expect($airport->refresh())
        ->icao_code->toBe($newData->icao_code);
});

it('can validate edit input', function () {
    $this->actingAs(User::factory()->system()->create());

    $airport = Airport::factory()->create();
    $newData = Airport::factory()->make();

    livewire(AirportResource\Pages\EditAirport::class, [
        'record' => $airport->getKey(),
    ])
        ->set('data.icao_code', null)
        ->call('save')
        ->assertHasErrors(['data.icao_code' => 'required']);
});

it('can render view page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportResource::getUrl('view', [
        'record' => Airport::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportResource::getUrl('view', [
        'record' => Airport::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportResource::getUrl('view', [
        'record' => Airport::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportResource::getUrl('view', [
        'record' => Airport::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for view page', function () {
    /** @var FrontendTestCase $this */
    $airport = Airport::factory()->create();

    livewire(AirportResource\Pages\ViewAirport::class, [
        'record' => $airport->getKey(),
    ])->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    livewire(AirportResource\Pages\ViewAirport::class, [
        'record' => $airport->getKey(),
    ])->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(AirportResource\Pages\ViewAirport::class, [
        'record' => $airport->getKey(),
    ])->assertSet('data.icao_code', $airport->icao_code);
});
