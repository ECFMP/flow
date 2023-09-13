<?php

namespace App\Filament\Resources\FlightInformationRegionResource\RelationManagers;

use App\Models\DiscordTag;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;

class DiscordTagsRelationManager extends RelationManager
{
    protected static string $relationship = 'discordTags';

    protected static ?string $recordTitleAttribute = 'tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('tag')
                    ->default('@')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignorable: fn (?Model $record): ?Model => $record)
                    ->rule('starts_with:@'),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tag'),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->form(fn (AttachAction $action) => [
                        $action->getRecordSelect()
                            ->getSearchResultsUsing(fn (string $query, DiscordTagsRelationManager $livewire) => DiscordTag::whereDoesntHave('flightInformationRegions', function (Builder $fir) use ($livewire) {
                                $fir->where('flight_information_regions.id', $livewire->ownerRecord->id);
                            })
                            ->where(function (Builder $subquery) use ($query) {
                                $subquery->where('tag', 'like', '%' . $query . '%')
                                ->orWhere('description', 'like', '%' . $query . '%');
                            })
                            ->get()
                            ->mapWithKeys(fn (DiscordTag $tag) => [$tag->id => sprintf('%s (%s)', $tag->description, $tag->tag)])
                            ->toArray())
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
