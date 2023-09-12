<?php

namespace App\Filament\Resources\DivisionDiscordWebhookResource\Pages;

use App\Filament\Resources\DivisionDiscordWebhookResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisionDiscordWebhooks extends ListRecords
{
    protected static string $resource = DivisionDiscordWebhookResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
