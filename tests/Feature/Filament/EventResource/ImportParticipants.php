<?php

use App\Models\User;
use App\Models\Event;
use App\Models\FlightInformationRegion;
use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\Pages\ImportParticipants;

use function Pest\Livewire\livewire;

it('can render page', function () {
    $firstFir = FlightInformationRegion::factory()->create();
    $secondFir = FlightInformationRegion::factory()->create();

    $firstEvent = Event::factory()->notStarted()->create([
        'flight_information_region_id' => $firstFir->getKey(),
    ]);
    $secondEvent = Event::factory()->notStarted()->create([
        'flight_information_region_id' => $secondFir->getKey(),
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

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $firstEvent,
    ]))->assertForbidden();

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $secondEvent,
    ]))->assertForbidden();

    $this->actingAs($flowManager);

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $firstEvent,
    ]))->assertSuccessful();

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $secondEvent,
    ]))->assertForbidden();

    $this->actingAs($nmt);

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $firstEvent,
    ]))->assertSuccessful();

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $secondEvent,
    ]))->assertSuccessful();

    $this->actingAs($system);

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $firstEvent,
    ]))->assertSuccessful();

    $this->get(EventResource::getUrl('import-participants', [
        'record' => $secondEvent,
    ]))->assertSuccessful();
});

it('can validate input', function () {
    /** @var FrontendTestCase $this */

    $fir = FlightInformationRegion::factory()->create();
    $event = Event::factory()->notStarted()->create([
        'flight_information_region_id' => $fir->getKey(),
    ]);

    $user = User::factory()->system()->create();
    $user->flightInformationRegions()->sync($fir->getKey());
    $this->actingAs($user);

    livewire(ImportParticipants::class, ['record' => $event])
        ->call('submit')
        ->assertHasErrors(['file' => 'required']);
});
