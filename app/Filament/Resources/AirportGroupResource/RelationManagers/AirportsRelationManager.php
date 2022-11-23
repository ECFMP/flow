<?php

namespace App\Filament\Resources\AirportGroupResource\RelationManagers;

use App\Filament\Helpers\HasCoordinateInputs;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\RelationManagers\RelationManager;

class AirportsRelationManager extends RelationManager
{
    use HasCoordinateInputs;

    protected static string $relationship = 'airports';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected static ?string $inverseRelationship = 'groups';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('icao_code')
                    ->label(__('ICAO code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->length(4),
                ...self::coordinateInputs(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')->label(__('ICAO code')),
            ])
            ->filters([
                //

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
