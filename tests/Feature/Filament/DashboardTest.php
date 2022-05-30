<?php

use App\Enums\FlowMeasureType;
use App\Filament\Resources\FlowMeasureResource\Widgets\ActiveFlowMeasures;
use App\Filament\Resources\FlowMeasureResource\Widgets\NotifiedFlowMeasures;
use App\Models\FlowMeasure;
use App\Models\User;
use Tests\FrontendTestCase;
use Filament\Pages\Dashboard;

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

test('NotifiedFlowMeasures: it shows empty', function () {
    /** @var FrontendTestCase $this */
    livewire(NotifiedFlowMeasures::class)->assertSee('No records found');
});

test('NotifiedFlowMeasures: it shows notified flow measures', function () {
    /** @var FrontendTestCase $this */

    $notNotifiedFlowMeasure = FlowMeasure::factory()->notNotified()->create();
    $upcomingFlowMeasure = FlowMeasure::factory()->notStarted()->create();
    $activeFlowMeasure = FlowMeasure::factory()->withMeasure(FlowMeasureType::MINIMUM_DEPARTURE_INTERVAL, 120)->create();
    $endedFlowMeasure = FlowMeasure::factory()->finished()->create();

    livewire(NotifiedFlowMeasures::class)
        ->assertDontSee($notNotifiedFlowMeasure->identifier)
        ->assertDontSee($activeFlowMeasure->identifier)
        ->assertDontSee($endedFlowMeasure->identifier)
        ->assertSee($upcomingFlowMeasure->identifier);
});
