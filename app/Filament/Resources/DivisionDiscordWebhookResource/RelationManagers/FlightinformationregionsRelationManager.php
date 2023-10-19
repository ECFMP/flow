<?php

namespace App\Filament\Resources\DivisionDiscordWebhookResource\RelationManagers;

use App\Models\FlightInformationRegion;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;

class FlightinformationregionsRelationManager extends RelationManager
{
    protected static string $relationship = 'flightInformationRegions';

    protected static ?string $title = 'Flight Information Regions';

    protected static ?string $recordTitleAttribute = 'identifier';

    protected static ?string $inverseRelationship = 'divisionDiscordWebhooks';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fir')
                    ->label(__('FIR'))
                    ->formatStateUsing(fn (FlightInformationRegion $record) => $record->identifierName),
                Tables\Columns\TextColumn::make('tag')
                    ->label(__('Mention Tag')),
            ])
            ->headerActions([
                AttachAction::make('attach-fir')
                    ->form(fn (AttachAction $action) => [
                        $action->getRecordSelect(),
                    TextInput::make('tag')
                        ->label(__('Discord Tag'))
                        ->helperText(__('A Discord tag for a person / group / role that should be mentioned in the post.'))
                        ->maxLength(255)
                    ]),
            ])
            ->actions([
                DetachAction::make(),
            ]);
    }
}
