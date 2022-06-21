<?php

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Models\User;
use Tests\FrontendTestCase;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var FrontendTestCase $this */
    $this->get(EventResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(EventResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(EventResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(EventResource::getUrl())->assertSuccessful();
});

it('can render create page', function () {
    /** @var FrontendTestCase $this */
    $this->get(EventResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(EventResource::getUrl('create'))->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(EventResource::getUrl('create'))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(EventResource::getUrl('create'))->assertSuccessful();
});

it('can create', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $newData = Event::factory()->make();

    livewire(EventResource\Pages\CreateEvent::class)
        ->set('data.name', $newData->name)
        ->set('data.date_start', $newData->date_start)
        ->set('data.date_end', $newData->date_end)
        ->set('data.flight_information_region_id', $newData->flight_information_region_id)
        ->set('data.vatcan_code', $newData->vatcan_code)
        ->call('create');

    $this->assertDatabaseHas(Event::class, [
        'name' => $newData->name,
        'date_start' => $newData->date_start->startOfMinute(),
        'date_end' => $newData->date_end->startOfMinute(),
        'flight_information_region_id' => $newData->flight_information_region_id,
        'vatcan_code' => $newData->vatcan_code,
    ]);
});

test('CreateEvent: End date changes when Start date is changed', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $newData = Event::factory()->make();

    livewire(EventResource\Pages\CreateEvent::class)
        ->set('data.date_start', $newData->date_start)
        ->assertSet('data.date_end', $newData->date_start->addHours(4));
});

it('can validate create input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    livewire(EventResource\Pages\CreateEvent::class)
        ->set('data.name', null)
        ->call('create')
        ->assertHasErrors(['data.name' => 'required']);
});

it('can render edit page', function () {
    $firstFir = FlightInformationRegion::factory()->create();
    $secondFir = FlightInformationRegion::factory()->create();

    $firstEvent = Event::factory()->create([
        'flight_information_region_id' => $firstFir->getKey(),
        'date_start' => now()->addDay(),
        'date_end' => now()->addDay()->addHour(),
    ]);
    $secondEvent = Event::factory()->create([
        'flight_information_region_id' => $secondFir->getKey(),
        'date_start' => now()->addDay(),
        'date_end' => now()->addDay()->addHour(),
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

    $this->get(EventResource::getUrl('edit', [
        'record' => $firstEvent,
    ]))->assertForbidden();

    $this->get(EventResource::getUrl('edit', [
        'record' => $secondEvent,
    ]))->assertForbidden();

    $this->actingAs($flowManager);

    $this->get(EventResource::getUrl('edit', [
        'record' => $firstEvent,
    ]))->assertSuccessful();

    $this->get(EventResource::getUrl('edit', [
        'record' => $secondEvent,
    ]))->assertForbidden();

    $this->actingAs($nmt);

    $this->get(EventResource::getUrl('edit', [
        'record' => $firstEvent,
    ]))->assertSuccessful();

    $this->get(EventResource::getUrl('edit', [
        'record' => $secondEvent,
    ]))->assertSuccessful();

    $this->actingAs($system);

    $this->get(EventResource::getUrl('edit', [
        'record' => $firstEvent,
    ]))->assertSuccessful();

    $this->get(EventResource::getUrl('edit', [
        'record' => $secondEvent,
    ]))->assertSuccessful();
});

it('can retrieve data for edit page', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());
    $event = Event::factory()->create();

    livewire(EventResource\Pages\EditEvent::class, [
        'record' => $event->getKey(),
    ])
        ->assertSet('data.name', $event->name)
        ->assertSet('data.flight_information_region_id', $event->flight_information_region_id)
        ->assertSet('data.date_start', $event->date_start->toDateTimeString())
        ->assertSet('data.date_end', $event->date_end->toDateTimeString())
        ->assertSet('data.vatcan_code', $event->vatcan_code);
});

it('can edit', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $event = Event::factory()->create();
    $newData = Event::factory()->make();

    livewire(EventResource\Pages\EditEvent::class, [
        'record' => $event->getKey(),
    ])
        ->set('data.name', $newData->name)
        ->set('data.date_start', $newData->date_start)
        ->set('data.date_end', $newData->date_end)
        ->set('data.flight_information_region_id', $newData->flight_information_region_id)
        ->set('data.vatcan_code', $newData->vatcan_code)
        ->call('save');

    expect($event->refresh())->toMatchArray([
        'name' => $newData->name,
        'date_start' => $newData->date_start->startOfMinute()->toISOString(),
        'date_end' => $newData->date_end->startOfMinute()->toISOString(),
        'flight_information_region_id' => $newData->flight_information_region_id,
        'vatcan_code' => $newData->vatcan_code,
    ]);
});

it('can validate edit input', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $event = Event::factory()->create();

    livewire(EventResource\Pages\EditEvent::class, [
        'record' => $event->getKey(),
    ])
        ->set('data.name', null)
        ->call('save')
        ->assertHasErrors(['data.name' => 'required']);
});

it('can render view page', function () {
    /** @var FrontendTestCase $this */
    $this->get(EventResource::getUrl('view', [
        'record' => Event::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(EventResource::getUrl('view', [
        'record' => Event::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(EventResource::getUrl('view', [
        'record' => Event::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(EventResource::getUrl('view', [
        'record' => Event::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for view page', function () {
    /** @var FrontendTestCase $this */
    $event = Event::factory()->create();

    livewire(EventResource\Pages\ViewEvent::class, [
        'record' => $event->getKey(),
    ])->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    livewire(EventResource\Pages\ViewEvent::class, [
        'record' => $event->getKey(),
    ])->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EventResource\Pages\ViewEvent::class, [
        'record' => $event->getKey(),
    ])->assertSet('data.name', $event->name)
        ->assertSet('data.flight_information_region_id', $event->flight_information_region_id)
        ->assertSet('data.date_start', $event->date_start->toDateTimeString())
        ->assertSet('data.date_end', $event->date_end->toDateTimeString())
        ->assertSet('data.vatcan_code', $event->vatcan_code);
});
