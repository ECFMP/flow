<?php

use App\Models\User;
use App\Models\FlowMeasure;
use Tests\FrontendTestCase;
use Filament\Pages\Dashboard;
use App\Enums\FlowMeasureType;
use App\Filament\Resources\EventResource\Widgets\UpcomingEvents;
use App\Filament\Resources\FlowMeasureResource\Widgets\ActiveFlowMeasures;
use App\Models\Event;

use function Pest\Livewire\livewire;

it('can render page', function () {
    /** @var FrontendTestCase $this */
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(Dashboard::getUrl())->assertSuccessful();
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
