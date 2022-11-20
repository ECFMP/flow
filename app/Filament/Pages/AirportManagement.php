<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AirportManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string $view = 'filament.pages.airport-management';

    public function getViewData(): array
    {
        return [
            'airportId' => 1,
        ];
    }
}
