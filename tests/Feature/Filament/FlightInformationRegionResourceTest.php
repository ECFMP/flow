<?php

use App\Models\User;
use App\Models\flightInformationRegion;
use Tests\FrontendTestCase;
use App\Filament\Resources\FlightInformationRegionResource;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var FrontendTestCase $this */
    $this->get(FlightInformationRegionResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(FlightInformationRegionResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(FlightInformationRegionResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(FlightInformationRegionResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(FlightInformationRegionResource::getUrl())->assertSuccessful();
});

it('can render edit page', function () {
    /** @var FrontendTestCase $this */
    $this->get(FlightInformationRegionResource::getUrl('edit', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(FlightInformationRegionResource::getUrl('edit', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(FlightInformationRegionResource::getUrl('edit', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(FlightInformationRegionResource::getUrl('edit', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(FlightInformationRegionResource::getUrl('edit', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for edit page', function () {
    $this->actingAs(User::factory()->system()->create());
    $flightInformationRegion = FlightInformationRegion::factory()->create();

    livewire(FlightInformationRegionResource\Pages\EditFlightInformationRegion::class, [
        'record' => $flightInformationRegion->getKey(),
    ])
        ->assertSet('data.identifier', $flightInformationRegion->identifier);
});

it('can edit', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $flightInformationRegion = FlightInformationRegion::factory()->create();
    $newData = FlightInformationRegion::factory()->make();

    livewire(FlightInformationRegionResource\Pages\EditFlightInformationRegion::class, [
        'record' => $flightInformationRegion->getKey(),
    ])
        ->set('data.identifier', $newData->identifier)
        ->call('save');

    expect($flightInformationRegion->refresh())
        ->identifier->toBe($newData->identifier);
});

it('can validate edit input', function () {
    $this->actingAs(User::factory()->system()->create());

    $flightInformationRegion = FlightInformationRegion::factory()->create();

    livewire(FlightInformationRegionResource\Pages\EditFlightInformationRegion::class, [
        'record' => $flightInformationRegion->getKey(),
    ])
        ->set('data.identifier', null)
        ->call('save')
        ->assertHasErrors(['data.identifier' => 'required']);
});

it('can render view page', function () {
    /** @var FrontendTestCase $this */
    $this->get(FlightInformationRegionResource::getUrl('view', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(FlightInformationRegionResource::getUrl('view', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(FlightInformationRegionResource::getUrl('view', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(FlightInformationRegionResource::getUrl('view', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(FlightInformationRegionResource::getUrl('view', [
        'record' => FlightInformationRegion::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for view page', function () {
    /** @var FrontendTestCase $this */
    $flightInformationRegion = FlightInformationRegion::factory()->create();

    livewire(FlightInformationRegionResource\Pages\ViewFlightInformationRegion::class, [
        'record' => $flightInformationRegion->getKey(),
    ])->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    livewire(FlightInformationRegionResource\Pages\ViewFlightInformationRegion::class, [
        'record' => $flightInformationRegion->getKey(),
    ])->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(FlightInformationRegionResource\Pages\ViewFlightInformationRegion::class, [
        'record' => $flightInformationRegion->getKey(),
    ])->assertSet('data.identifier', $flightInformationRegion->identifier);
});
