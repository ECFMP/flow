<?php

use App\Filament\Resources\DivisionDiscordWebhookResource;
use App\Filament\Resources\DivisionDiscordWebhookResource\Pages\CreateDivisionDiscordWebhook;
use App\Filament\Resources\DivisionDiscordWebhookResource\Pages\ListDivisionDiscordWebhooks;
use App\Filament\Resources\DivisionDiscordWebhookResource\Pages\EditDivisionDiscordWebhook;
use App\Models\DivisionDiscordWebhook;
use App\Models\User;

use Illuminate\Support\Str;

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

it('nmt and system have create action', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(ListDivisionDiscordWebhooks::class)
        ->assertPageActionExists('create');


    $this->actingAs(User::factory()->system()->create());
    livewire(ListDivisionDiscordWebhooks::class)
        ->assertPageActionExists('create');
});

it('nmt and system can create', function () {
    $this->get(DivisionDiscordWebhookResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('create'))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('create'))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('create'))->assertSuccessful();
});


it('can be created by nmt', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', 'test')
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(
        'division_discord_webhooks',
        [
            'description' => 'test',
            'url' => 'https://ecfmp.vatsim.net'
        ]
    );
});

it('can be created by system', function () {
    $this->actingAs(User::factory()->system()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', 'test')
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(
        'division_discord_webhooks',
        [
            'description' => 'test',
            'url' => 'https://ecfmp.vatsim.net'
        ]
    );
});

it('rejects missing descriptions on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('create')
        ->assertHasErrors(['data.description']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('rejects empty descriptions  on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', '')
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('create')
        ->assertHasErrors(['data.description']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('rejects large descriptions on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', Str::padRight('', 256, 'a'))
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('create')
        ->assertHasErrors(['data.description']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('rejects missing urls on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', 'abc')
        ->call('create')
        ->assertHasErrors(['data.url']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('rejects empty urls on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', 'abc')
        ->set('data.url', '')
        ->call('create')
        ->assertHasErrors(['data.url']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('rejects excessive urls on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', 'abc')
        ->set('data.url', Str::padRight('https://ecfmp.vatsim.net/', 501, 'a'))
        ->call('create')
        ->assertHasErrors(['data.url']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('rejects invalid urls on create', function () {
    $this->actingAs(User::factory()->networkManager()->create());
    livewire(CreateDivisionDiscordWebhook::class)
        ->set('data.description', 'abc')
        ->set('data.url', 'aaaaa')
        ->call('create')
        ->assertHasErrors(['data.url']);

    $this->assertDatabaseCount(
        'division_discord_webhooks',
        0
    );
});

it('nmt and system can edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->get(DivisionDiscordWebhookResource::getUrl('edit', ['record' => $webhook->id]))->assertForbidden();

    $this->actingAs(User::factory()->eventManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('edit', ['record' => $webhook->id]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('edit', ['record' => $webhook->id]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('edit', ['record' => $webhook->id]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(DivisionDiscordWebhookResource::getUrl('edit', ['record' => $webhook->id]))->assertSuccessful();
});

it('can be edited by nmt', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', 'test')
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(
        'division_discord_webhooks',
        [
            'id' => $webhook->id,
            'description' => 'test',
            'url' => 'https://ecfmp.vatsim.net'
        ]
    );
});

it('can be edited by system', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->system()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', 'test')
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(
        'division_discord_webhooks',
        [
            'id' => $webhook->id,
            'description' => 'test',
            'url' => 'https://ecfmp.vatsim.net'
        ]
    );
});

it('rejects missing descriptions on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', null)
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('save')
        ->assertHasErrors(['data.description']);
});

it('rejects empty descriptions on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', '')
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('save')
        ->assertHasErrors(['data.description']);
});

it('rejects large descriptions on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', Str::padRight('', 256, 'a'))
        ->set('data.url', 'https://ecfmp.vatsim.net')
        ->call('save')
        ->assertHasErrors(['data.description']);
});

it('rejects missing urls on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', 'abc')
        ->set('data.url', null)
        ->call('save')
        ->assertHasErrors(['data.url']);
});

it('rejects empty urls on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', 'abc')
        ->set('data.url', '')
        ->call('save')
        ->assertHasErrors(['data.url']);
});

it('rejects excessive urls on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', 'abc')
        ->set('data.url', Str::padRight('https://ecfmp.vatsim.net/', 501, 'a'))
        ->call('save')
        ->assertHasErrors(['data.url']);
});

it('rejects invalid urls on edit', function () {
    $webhook = DivisionDiscordWebhook::factory()->create();

    $this->actingAs(User::factory()->networkManager()->create());
    livewire(EditDivisionDiscordWebhook::class, ['record' => $webhook->id])
        ->set('data.description', 'abc')
        ->set('data.url', 'aaaaa')
        ->call('save')
        ->assertHasErrors(['data.url']);
});
