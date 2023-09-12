<?php

use App\Filament\Resources\DivisionDiscordWebhookResource;
use App\Filament\Resources\DivisionDiscordWebhookResource\Pages\ListDivisionDiscordWebhooks;
use App\Models\DivisionDiscordWebhook;
use App\Models\User;

use function Pest\Livewire\livewire;

it('can render index page with permissions', function () {
    /** @var FrontendTestCase $this */
    $this->get(DivisionDiscordWebhookResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl())->assertSuccessful();
});

it('nmt and system can create', function () {
    DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(ListDivisionDiscordWebhooks::class)
        ->assertPageActionExists('create');

    $this->actingAs(User::factory()->system()->create());
    livewire(ListDivisionDiscordWebhooks::class)
        ->assertPageActionExists('create');
});
