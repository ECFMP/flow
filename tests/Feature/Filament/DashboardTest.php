<?php

use App\Models\User;
use App\Models\Event;
use App\Models\FlowMeasure;
use Tests\FrontendTestCase;
use Filament\Pages\Dashboard;
use App\Enums\FlowMeasureType;
use App\Filament\Widgets\MyPermissions;
use App\Models\FlightInformationRegion;
use App\Filament\Resources\EventResource\Widgets\UpcomingEvents;
use App\Filament\Resources\FlowMeasureResource\Widgets\ActiveFlowMeasures;

use function Pest\Livewire\livewire;

it('can render page', function () {
    /** @var FrontendTestCase $this */
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();
});

test('MyPermissions: it renders for User', function () {
    /** @var FrontendTestCase $this */
    livewire(MyPermissions::class)
        ->assertSee('Normal User - View Only')
        ->assertDontSee('Flight Information Regions');
});

test('MyPermissions: it renders for EventManager', function () {
    /** @var FrontendTestCase $this */

    /** @var FlightInformationRegion $firstFir */
    $firstFir = FlightInformationRegion::factory()->create();

    /** @var FlightInformationRegion $secondFir */
    $secondFir = FlightInformationRegion::factory()->create();

    /** @var User $user */
    $user = User::factory()->eventManager()->create();

    $this->actingAs($user);

    livewire(MyPermissions::class)
        ->assertSee('Event Manager')
        ->assertSee('Flight Information Regions')
        ->assertSee('None');

    $user->flightInformationRegions()->sync($firstFir->id);
    $user->refresh();

    livewire(MyPermissions::class)
        ->assertSee($firstFir->identifier_name)
        ->assertDontSee($secondFir->identifier_name);
});

test('MyPermissions: it renders for FlowManager', function () {
    /** @var FrontendTestCase $this */

    /** @var FlightInformationRegion $firstFir */
    $firstFir = FlightInformationRegion::factory()->create();

    /** @var FlightInformationRegion $secondFir */
    $secondFir = FlightInformationRegion::factory()->create();

    /** @var User $user */
    $user = User::factory()->flowManager()->create();

    $this->actingAs($user);

    livewire(MyPermissions::class)
        ->assertSee('Flow Manager')
        ->assertSee('Flight Information Regions')
        ->assertSee('None');

    $user->flightInformationRegions()->sync($firstFir->id);
    $user->refresh();

    livewire(MyPermissions::class)
        ->assertSee($firstFir->identifier_name)
        ->assertDontSee($secondFir->identifier_name);
});

test('MyPermissions: it renders for NMT', function () {
    /** @var FrontendTestCase $this */

    /** @var FlightInformationRegion $firstFir */
    $firstFir = FlightInformationRegion::factory()->create();

    /** @var FlightInformationRegion $secondFir */
    $secondFir = FlightInformationRegion::factory()->create();

    /** @var User $user */
    $user = User::factory()->networkManager()->create();

    $this->actingAs($user);

    livewire(MyPermissions::class)
        ->assertSee('Network Management Team')
        ->assertSee('Flight Information Regions')
        ->assertSee('All');

    $user->flightInformationRegions()->sync($firstFir->id);
    $user->refresh();

    livewire(MyPermissions::class)
        ->assertSee('All')
        ->assertDontSee($firstFir->identifier_name)
        ->assertDontSee($secondFir->identifier_name);
});

test('MyPermissions: it renders for System', function () {
    /** @var FrontendTestCase $this */

    /** @var FlightInformationRegion $firstFir */
    $firstFir = FlightInformationRegion::factory()->create();

    /** @var FlightInformationRegion $secondFir */
    $secondFir = FlightInformationRegion::factory()->create();

    /** @var User $user */
    $user = User::factory()->system()->create();

    $this->actingAs($user);

    livewire(MyPermissions::class)
        ->assertSee('System user')
        ->assertSee('Flight Information Regions')
        ->assertSee('All');

    $user->flightInformationRegions()->sync($firstFir->id);
    $user->refresh();

    livewire(MyPermissions::class)
        ->assertSee('All')
        ->assertDontSee($firstFir->identifier_name)
        ->assertDontSee($secondFir->identifier_name);
});

test('ActiveFlowMeasures: it shows empty', function () {
    /** @var FrontendTestCase $this */
    livewire(ActiveFlowMeasures::class)->assertSee('No records found');
});

test('ActiveFlowMeasures: it shows active flow measures', function () {
    /** @var FrontendTestCase $this */

    $upcomingFlowMeasure = FlowMeasure::factory()->notStarted()->create();
    $activeFlowMeasure = FlowMeasure::factory()->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 120)->create();
    $endedFlowMeasure = FlowMeasure::factory()->finished()->create();

    livewire(ActiveFlowMeasures::class)
        ->assertDontSee($upcomingFlowMeasure->identifier)
        ->assertDontSee($endedFlowMeasure->identifier)
        ->assertSee($activeFlowMeasure->identifier);
});

test('UpcomingEvents: it shows empty', function () {
    /** @var FrontendTestCase $this */
    livewire(UpcomingEvents::class)->assertSee('No records found');
});


test('UpcomingEvents: it shows events', function () {
    $upcomingEvent = Event::factory()->notStarted()->create();
    $activeEvent = Event::factory()->create();
    $endedEvent = Event::factory()->finished()->create();

    /** @var FrontendTestCase $this */
    livewire(UpcomingEvents::class)
        ->assertSee($upcomingEvent->name)
        ->assertDontSee($endedEvent->name)
        ->assertSee($activeEvent->name);
});
