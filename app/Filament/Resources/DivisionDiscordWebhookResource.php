<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionDiscordWebhookResource\Pages;
use App\Models\DivisionDiscordWebhook;
use App\Models\FlightInformationRegion;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;

class DivisionDiscordWebhookResource extends Resource
{
    protected static ?string $model = DivisionDiscordWebhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label(__('Description'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->url()
                    ->required()
                    ->maxLength(500
                    )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('id')),
                    TextColumn::make('description')
                    ->label(__('description')),
                TextColumn::make('url')
                    ->label(__('URL')),
                TagsColumn::make('firs')
                    ->getStateUsing(fn (DivisionDiscordWebhook $record) => $record->flightInformationRegions->map(fn (FlightInformationRegion $fir) => $fir->identifierName)->toArray())
                    ->label(__('FIRs')),
                TextColumn::make('created_at')
                    ->label(__('Created At')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisionDiscordWebhooks::route('/'),
            'create' => Pages\CreateDivisionDiscordWebhook::route('/create'),
            'edit' => Pages\EditDivisionDiscordWebhook::route('/{record}/edit'),
        ];
    }
}
