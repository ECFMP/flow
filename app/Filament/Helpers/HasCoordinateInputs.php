<?php

namespace App\Filament\Helpers;

use Filament\Forms\Components\TextInput;

trait HasCoordinateInputs
{
    public static function latitudeInput(): TextInput
    {
        return TextInput::make('latitude')
            ->required()
            ->numeric('decimal:7')
            ->minValue(-90)
            ->maxValue(90)
            ->label('Latitude')
            ->label('In decimal degrees');
    }

    public static function longitudeInput(): TextInput
    {
        return TextInput::make('longitude')
            ->required()
            ->numeric('decimal:7')
            ->minValue(-180)
            ->maxValue(180)
            ->label('Longitude')
            ->helperText('In decimal degrees');
    }

    public static function coordinateInputs(): array
    {
        return [self::latitudeInput(), self::longitudeInput()];
    }
}
