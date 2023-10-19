<?php

namespace App\Filament\Resources\DivisionDiscordWebhookResource\Pages;

use App\Filament\Resources\DivisionDiscordWebhookResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivisionDiscordWebhook extends EditRecord
{
    protected static string $resource = DivisionDiscordWebhookResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
