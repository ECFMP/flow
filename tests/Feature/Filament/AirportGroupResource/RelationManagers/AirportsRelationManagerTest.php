<?php

use App\Models\User;
use App\Models\Airport;
use Tests\FrontendTestCase;
use App\Models\AirportGroup;
use Filament\Tables\Actions\CreateAction;
use App\Filament\Resources\AirportGroupResource;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\EditAction;

use function Pest\Livewire\livewire;

it('can render relation manager', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->has(Airport::factory()->count(10))
        ->create();

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])
        ->assertSuccessful();
});

it('can list airports', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->has(Airport::factory()->count(10))
        ->create();

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])
        ->assertCanSeeTableRecords($airportGroup->airports);
});

it('can create airport', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    /** @var Airport $airport */
    $airport = Airport::factory()->make();

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(CreateAction::class, data: [
            'icao_code' => $airport->icao_code,
            'latitude' => $airport->latitude,
            'longitude' => $airport->longitude,
        ])->assertHasNoTableActionErrors();

    $this->assertDatabaseHas(Airport::class, [
        'icao_code' => $airport->icao_code,
        'latitude' => $airport->latitude,
        'longitude' => $airport->longitude,
    ]);
});

it('can validate create input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(CreateAction::class, data: [
            'icao_code' => null,
            'latitude' => null,
            'longitude' => null,
        ])->assertHasTableActionErrors(['icao_code' => 'required', 'latitude' => 'required', 'longitude' => 'required']);
});

it('can edit airport', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var Airport $airport */
    $airport = Airport::factory()->create();

    /** @var Airport $newData */
    $newData = $airport->factory()->make();

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    $airportGroup->airports()->attach($airport->getKey());

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(EditAction::class, $airport, [
            'icao_code' => $newData->icao_code,
            'latitude' => $newData->latitude,
            'longitude' => $newData->longitude,
        ])->assertHasNoTableActionErrors();

    $this->assertDatabaseHas(Airport::class, [
        'icao_code' => $newData->icao_code,
        'latitude' => $newData->latitude,
        'longitude' => $newData->longitude,
    ]);
});

it('can validate edit input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var Airport $airport */
    $airport = Airport::factory()->create();

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    $airportGroup->airports()->attach($airport->getKey());

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(EditAction::class, $airport, [
            'icao_code' => null,
            'latitude' => null,
            'longitude' => null,
        ])->assertHasTableActionErrors(['icao_code' => 'required', 'latitude' => 'required', 'longitude' => 'required']);
});

it('can attach airport', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    /** @var Airport $airport */
    $airport = Airport::factory()->create();

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(AttachAction::class, $airport, [
            'recordId' => $airport->getKey(),
        ])->assertHasNoTableActionErrors()
        ->assertCanSeeTableRecords([$airport]);
});

it('can validate attach input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    /** @var Airport $airport */
    $airport = Airport::factory()->create();

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(AttachAction::class, $airport, [
            'recordId' => null,
        ])->assertHasTableActionErrors(['recordId' => 'required']);
});

it('can detach airport', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    /** @var AirportGroup $airportGroup */
    $airportGroup = AirportGroup::factory()
        ->create();

    /** @var Airport $airport */
    $airport = Airport::factory()->create();

    $airportGroup->airports()->attach($airport->getKey());

    livewire(AirportGroupResource\RelationManagers\AirportsRelationManager::class, [
        'ownerRecord' => $airportGroup,
    ])->callTableAction(
        DetachAction::class,
        $airport->getKey()
    )->assertHasNoTableActionErrors()
        ->assertCanNotSeeTableRecords([$airport]);
});
