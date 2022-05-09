<?php

use App\Models\User;
use Tests\FrontendTestCase;
use Filament\Pages\Dashboard;

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
