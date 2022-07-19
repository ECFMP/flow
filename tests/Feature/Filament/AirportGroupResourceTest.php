<?php

use App\Filament\Resources\AirportGroupResource;
use App\Models\AirportGroup;
use App\Models\User;
use Tests\FrontendTestCase;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportGroupResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(AirportGroupResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportGroupResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportGroupResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportGroupResource::getUrl())->assertSuccessful();
});

it('can render create page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportGroupResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(AirportGroupResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportGroupResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportGroupResource::getUrl('create'))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportGroupResource::getUrl('create'))->assertSuccessful();
});

it('can create', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $newData = AirportGroup::factory()->make();

    livewire(AirportGroupResource\Pages\CreateAirportGroup::class)
        ->set('data.name', $newData->name)
        ->call('create');

    $this->assertDatabaseHas(AirportGroup::class, [
        'name' => $newData->name
    ]);
});

it('can validate create input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    livewire(AirportGroupResource\Pages\CreateAirportGroup::class)
        ->set('data.name', null)
        ->call('create')
        ->assertHasErrors(['data.name' => 'required']);
});

it('can render edit page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportGroupResource::getUrl('edit', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(AirportGroupResource::getUrl('edit', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportGroupResource::getUrl('edit', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportGroupResource::getUrl('edit', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportGroupResource::getUrl('edit', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for edit page', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());
    $airportGroup = AirportGroup::factory()->create();

    livewire(AirportGroupResource\Pages\EditAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])
        ->assertSet('data.name', $airportGroup->name);
});

it('can edit', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $airportGroup = AirportGroup::factory()->create();
    $newData = AirportGroup::factory()->make();

    livewire(AirportGroupResource\Pages\EditAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])
        ->set('data.name', $newData->name)
        ->call('save');

    expect($airportGroup->refresh())
        ->name->toBe($newData->name);
});

it('can validate edit input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $airportGroup = AirportGroup::factory()->create();
    $newData = AirportGroup::factory()->make();

    livewire(AirportGroupResource\Pages\EditAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])
        ->set('data.name', null)
        ->call('save')
        ->assertHasErrors(['data.name' => 'required']);
});

it('can render view page', function () {
    /** @var FrontendTestCase $this */
    $this->get(AirportGroupResource::getUrl('view', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(AirportGroupResource::getUrl('view', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(AirportGroupResource::getUrl('view', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(AirportGroupResource::getUrl('view', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(AirportGroupResource::getUrl('view', [
        'record' => AirportGroup::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for view page', function () {
    /** @var FrontendTestCase $this */
    $airportGroup = AirportGroup::factory()->create();

    livewire(AirportGroupResource\Pages\ViewAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    livewire(AirportGroupResource\Pages\ViewAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    livewire(AirportGroupResource\Pages\ViewAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(AirportGroupResource\Pages\ViewAirportGroup::class, [
        'record' => $airportGroup->getKey(),
    ])->assertSet('data.name', $airportGroup->name);
});
