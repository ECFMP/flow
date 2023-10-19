<?php

use App\Filament\Resources\DivisionDiscordWebhookResource\RelationManagers\FlightinformationRegionsRelationManager;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\FrontendTestCase;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;

use function Pest\Livewire\livewire;

it('can render relation manager', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $webhook = DivisionDiscordWebhook::factory()->create();

    livewire(FlightinformationRegionsRelationManager::class, [
        'ownerRecord' => $webhook,
    ])
        ->assertSuccessful();
});

it('can save relation without tag', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $webhook = DivisionDiscordWebhook::factory()->create();
    $fir = FlightInformationRegion::factory()->create();

    livewire(FlightinformationRegionsRelationManager::class, [
        'ownerRecord' => $webhook,
    ])
        ->callTableAction('attach-fir', data: [
            'recordId' => $fir->id,
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas(
        'division_discord_webhook_flight_information_region',
        [
            'division_discord_webhook_id' => $webhook->id,
            'flight_information_region_id' => $fir->id,
            'tag' => null,
        ]
    );
});

it('can save relation with tag', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $webhook = DivisionDiscordWebhook::factory()->create();
    $fir = FlightInformationRegion::factory()->create();

    livewire(FlightinformationRegionsRelationManager::class, [
        'ownerRecord' => $webhook,
    ])
        ->callTableAction('attach-fir', data: [
            'recordId' => $fir->id,
            'tag' => 'abc'
        ])
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas(
        'division_discord_webhook_flight_information_region',
        [
            'division_discord_webhook_id' => $webhook->id,
            'flight_information_region_id' => $fir->id,
            'tag' => 'abc',
        ]
    );
});

it('doesnt save relation with long tag', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $webhook = DivisionDiscordWebhook::factory()->create();
    $fir = FlightInformationRegion::factory()->create();

    livewire(FlightinformationRegionsRelationManager::class, [
        'ownerRecord' => $webhook,
    ])
        ->callTableAction('attach-fir', data: [
            'recordId' => $fir->id,
            'tag' => Str::padRight('', 256, 'a')
        ])
        ->assertHasTableActionErrors(['tag']);

    $this->assertDatabaseMissing(
        'division_discord_webhook_flight_information_region',
        [
            'division_discord_webhook_id' => $webhook->id,
            'flight_information_region_id' => $fir->id,
        ]
    );
});
