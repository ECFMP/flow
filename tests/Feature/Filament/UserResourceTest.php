<?php

use App\Filament\Resources\UserResource;
use App\Models\User;
use Tests\FrontendTestCase;

use function Pest\Livewire\livewire;

it('can render index page', function () {
    /** @var FrontendTestCase $this */
    $this->get(UserResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(UserResource::getUrl())->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(UserResource::getUrl())->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(UserResource::getUrl())->assertSuccessful();
});

it('can render edit page', function () {
    /** @var FrontendTestCase $this */
    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->flowManager()->create());
    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertForbidden();

    $this->actingAs(User::factory()->networkManager()->create());
    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertSuccessful();

    $this->actingAs(User::factory()->system()->create());
    $this->get(UserResource::getUrl('edit', [
        'record' => User::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data for edit page', function () {
    $this->actingAs(User::factory()->system()->create());
    $user = User::factory()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getKey(),
    ])
        ->assertSet('data.id', $user->id)
        ->assertSet('data.name', $user->name)
        ->assertSet('data.role', $user->role->value);
});

it('can edit', function () {
    /** @var FrontendTestCase $this */
    $this->actingAs(User::factory()->system()->create());

    $user = User::factory()->create();
    $newData = User::factory()->make();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getKey(),
    ])
        ->set('data.role_id', $newData->role_id)
        ->call('save');

    expect($user->refresh())
        ->role_id->toBe($newData->role_id);
});

it('can validate edit input', function () {
    $this->actingAs(User::factory()->system()->create());

    $user = User::factory()->create();

    livewire(UserResource\Pages\EditUser::class, [
        'record' => $user->getKey(),
    ])
        ->set('data.role_id', null)
        ->call('save')
        ->assertHasErrors(['data.role_id' => 'required']);
});
