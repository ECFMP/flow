<?php

use App\Enums\FlowMeasureType;
use App\Filament\Resources\FlowMeasureResource;
use App\Models\FlowMeasure;
use App\Models\FlightInformationRegion;
use App\Models\User;
use Tests\FrontendTestCase;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var FrontendTestCase $this */
    $this->get(FlowMeasureResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(FlowMeasureResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(FlowMeasureResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(FlowMeasureResource::getUrl())->assertSuccessful();
});

it('can render create page', function () {
    /** @var FrontendTestCase $this */
    $this->get(FlowMeasureResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(FlowMeasureResource::getUrl('create'))->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(FlowMeasureResource::getUrl('create'))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(FlowMeasureResource::getUrl('create'))->assertSuccessful();
});

it('can create', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $newData = FlowMeasure::factory()->notStarted()->make();

    // TODO: Also check filters
    livewire(FlowMeasureResource\Pages\CreateFlowMeasure::class)
        ->set('data.flight_information_region_id', $newData->flight_information_region_id)
        ->set('data.event_id', $newData->event_id)
        ->set('data.start_time', $newData->start_time)
        ->set('data.end_time', $newData->end_time)
        ->set('data.reason', $newData->reason)
        ->set('data.type', $newData->type->value)
        ->set('data.value', $newData->value)
        ->set('data.mandatory_route', $newData->mandatory_route)
        ->call('create');

    $this->assertDatabaseHas(FlowMeasure::class, [
        'flight_information_region_id' => $newData->flight_information_region_id,
        'event_id' => $newData->event_id,
        'start_time' => $newData->start_time,
        'end_time' => $newData->end_time,
        'reason' => $newData->reason,
        'type' => $newData->type,
        'value' => $newData->value,
        'mandatory_route' => $newData->mandatory_route,
    ]);
});

it('can validate create input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    livewire(FlowMeasureResource\Pages\CreateFlowMeasure::class)
        ->set('data.start_time', null)
        ->call('create')
        ->assertHasErrors(['data.start_time' => 'required']);
});

it('can render edit page', function () {
    $firstFir = FlightInformationRegion::factory()->create();
    $secondFir = FlightInformationRegion::factory()->create();

    $firstFlowMeasure = FlowMeasure::factory()->create([
        'flight_information_region_id' => $firstFir->getKey(),
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);
    $secondFlowMeasure = FlowMeasure::factory()->create([
        'flight_information_region_id' => $secondFir->getKey(),
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    /** @var FrontendTestCase $this */
    /** @var User $user */
    $user = User::factory()->create();

    /** @var User $flowManager */
    $flowManager = User::factory()->flowManager()->create();
    $flowManager->flightInformationRegions()->sync($firstFir->getKey());

    /** @var User $nmt */
    $nmt = User::factory()->networkManager()->create();
    $nmt->flightInformationRegions()->sync($firstFir->getKey());

    /** @var User $system */
    $system = User::factory()->system()->create();
    $system->flightInformationRegions()->sync($firstFir->getKey());

    $this->actingAs($user);

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $firstFlowMeasure,
    ]))->assertForbidden();

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $secondFlowMeasure,
    ]))->assertForbidden();

    $this->actingAs($flowManager);

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $firstFlowMeasure,
    ]))->assertSuccessful();

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $secondFlowMeasure,
    ]))->assertForbidden();

    $this->actingAs($nmt);

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $firstFlowMeasure,
    ]))->assertSuccessful();

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $secondFlowMeasure,
    ]))->assertSuccessful();

    $this->actingAs($system);

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $firstFlowMeasure,
    ]))->assertSuccessful();

    $this->get(FlowMeasureResource::getUrl('edit', [
        'record' => $secondFlowMeasure,
    ]))->assertSuccessful();
});

it('can retrieve data for edit page', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());
    $flowMeasure = FlowMeasure::factory()->create();

    livewire(FlowMeasureResource\Pages\EditFlowMeasure::class, [
        'record' => $flowMeasure->getKey(),
    ])
        ->assertSet('data.flight_information_region_id', $flowMeasure->flight_information_region_id)
        ->assertSet('data.event_id', $flowMeasure->event_id)
        ->assertSet('data.start_time', $flowMeasure->start_time->toISOString())
        ->assertSet('data.end_time', $flowMeasure->end_time->toISOString())
        ->assertSet('data.reason', $flowMeasure->reason)
        ->assertSet('data.type', $flowMeasure->type)
        ->assertSet('data.value', $flowMeasure->value)
        ->assertSet('data.mandatory_route', $flowMeasure->mandatory_route ?? []);
});

it('can edit', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $flowMeasure = FlowMeasure::factory()->create();
    $newData = FlowMeasure::factory()->make();

    livewire(FlowMeasureResource\Pages\EditFlowMeasure::class, [
        'record' => $flowMeasure->getKey(),
    ])
        ->set('data.flight_information_region_id', $newData->flight_information_region_id)
        ->set('data.event_id', $newData->event_id)
        ->set('data.reason', $newData->reason)
        ->set('data.type', $newData->type->value)
        ->set('data.value', $newData->value)
        ->set('data.mandatory_route', $newData->mandatory_route)
        ->call('save');

    expect($flowMeasure->refresh())->toMatchArray([
        'flight_information_region_id' => $newData->flight_information_region_id,
        'event_id' => $newData->event_id,
        'reason' => $newData->reason,
        'type' => $newData->type,
        'value' => $newData->value,
        'mandatory_route' => $newData->mandatory_route,
    ]);
});

it('can validate edit input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $flowMeasure = FlowMeasure::factory()->create();

    livewire(FlowMeasureResource\Pages\EditFlowMeasure::class, [
        'record' => $flowMeasure->getKey(),
    ])
        ->set('data.reason', null)
        ->call('save')
        ->assertHasErrors(['data.reason' => 'required']);
});

it('can render view page', function () {
    /** @var FrontendTestCase $this */
    $this->get(FlowMeasureResource::getUrl('view', [
        'record' => FlowMeasure::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(FlowMeasureResource::getUrl('view', [
        'record' => FlowMeasure::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(FlowMeasureResource::getUrl('view', [
        'record' => FlowMeasure::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(FlowMeasureResource::getUrl('view', [
        'record' => FlowMeasure::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for view page', function () {
    /** @var FrontendTestCase $this */
    $flowMeasure = FlowMeasure::factory()->create();

    livewire(FlowMeasureResource\Pages\ViewFlowMeasure::class, [
        'record' => $flowMeasure->getKey(),
    ])->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    livewire(FlowMeasureResource\Pages\ViewFlowMeasure::class, [
        'record' => $flowMeasure->getKey(),
    ])->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(FlowMeasureResource\Pages\ViewFlowMeasure::class, [
        'record' => $flowMeasure->getKey(),
    ])->assertSet('data.flight_information_region_id', $flowMeasure->flight_information_region_id)
        ->assertSet('data.event_id', $flowMeasure->event_id)
        ->assertSet('data.start_time', $flowMeasure->start_time->toISOString())
        ->assertSet('data.end_time', $flowMeasure->end_time->toISOString())
        ->assertSet('data.reason', $flowMeasure->reason)
        ->assertSet('data.type', $flowMeasure->type)
        ->assertSet('data.value', $flowMeasure->value)
        ->assertSet('data.mandatory_route', $flowMeasure->mandatory_route ?? []);
});