<?php

namespace App\Filament\Resources\FlowMeasureResource\Traits;

use Closure;
use Filament\Forms;
use App\Models\Event;
use Filament\Pages\Page;
use App\Models\AirportGroup;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Builder\Block;
use App\Filament\Resources\FlowMeasureResource;

trait Filters
{
    public static function filters(Collection $events): Fieldset
    {
        return Forms\Components\Fieldset::make(__('Filters'))->schema([
            Forms\Components\Repeater::make('adep')
                ->label('ADEP')
                ->required()
                ->label('Departure(s) [ADEP]')
                ->disableItemMovement()
                ->disableItemCreation(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                ->disabled(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                ->hintIcon('heroicon-o-trending-up')
                ->schema([
                    Forms\Components\Select::make('value_type')
                        ->options([
                            'airport_group' => __('Airport Group'),
                            'custom_value' => __('Custom value'),
                        ])
                        ->default('custom_value')
                        ->reactive()
                        ->required(),
                    Forms\Components\Select::make('airport_group')
                        ->hintIcon('heroicon-o-collection')
                        ->visible(fn (Closure $get) => $get('value_type') == 'airport_group')
                        ->searchable()
                        ->options(AirportGroup::all()->pluck('name_codes', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('custom_value')
                        ->visible(fn (Closure $get) => $get('value_type') == 'custom_value')
                        ->length(4)
                        ->default('****')
                        ->required()
                ]),
            Forms\Components\Repeater::make('ades')
                ->label('ADES')
                ->required()
                ->label('Arrival(s) [ADES]')
                ->disableItemMovement()
                ->disableItemCreation(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                ->disabled(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                ->hintIcon('heroicon-o-trending-down')
                ->schema([
                    Forms\Components\Select::make('value_type')
                        ->options([
                            'airport_group' => __('Airport Group'),
                            'custom_value' => __('Custom value'),
                        ])
                        ->default('custom_value')
                        ->reactive()
                        ->required(),
                    Forms\Components\Select::make('airport_group')
                        ->hintIcon('heroicon-o-collection')
                        ->visible(fn (Closure $get) => $get('value_type') == 'airport_group')
                        ->searchable()
                        ->options(AirportGroup::all()->pluck('name_codes', 'id'))
                        ->required(),
                    Forms\Components\TextInput::make('custom_value')
                        ->visible(fn (Closure $get) => $get('value_type') == 'custom_value')
                        ->length(4)
                        ->default('****')
                        ->required()
                ]),
            Forms\Components\Builder::make('filters')
                ->label('Optional filters')
                ->createItemButtonLabel(__('Add optional filter'))
                ->columnSpan('full')
                ->inset()
                ->disableItemCreation(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                ->disableItemMovement()
                ->disabled(fn (Closure $get) => $get('edit_mode') == FlowMeasureResource::PARTIAL_EDIT)
                ->blocks([
                    Block::make('waypoint')
                        ->icon('heroicon-o-view-list')
                        ->schema([
                            Forms\Components\Textarea::make('value')
                                ->label(__('Waypoint'))
                                ->hintIcon('heroicon-o-view-list')
                                ->required()
                        ]),
                    Block::make('level_above')
                        ->icon('heroicon-o-arrow-up')
                        ->schema([
                            // TODO: Add mask?
                            Forms\Components\TextInput::make('value')
                                ->label(__('Level above'))
                                ->hintIcon('heroicon-o-arrow-up')
                                ->numeric()
                                ->step(5)
                                ->prefix('FL')
                                ->minLength(0)
                                ->maxLength(660)
                                ->required()
                        ]),
                    Block::make('level_below')
                        ->icon('heroicon-o-arrow-down')
                        ->schema([
                            // TODO: Add mask?
                            Forms\Components\TextInput::make('value')
                                ->label(__('Level below'))
                                ->hintIcon('heroicon-o-arrow-down')
                                ->numeric()
                                ->step(5)
                                ->prefix('FL')
                                ->minLength(0)
                                ->maxLength(660)
                                ->required()
                        ]),
                    Block::make('level')
                        ->icon('heroicon-o-arrow-right')
                        ->schema([
                            // TODO: Add mask?
                            Forms\Components\TextInput::make('value')
                                ->label(__('Level'))
                                ->hintIcon('heroicon-o-arrow-right')
                                ->numeric()
                                ->step(5)
                                ->prefix('FL')
                                ->minLength(0)
                                ->maxLength(660)
                                ->required()
                        ]),
                    Block::make('member_event')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\Select::make('member_event')
                                ->label(__('Event'))
                                ->hintIcon('heroicon-o-calendar')
                                ->hintIcon('heroicon-o-calendar')
                                ->searchable()
                                ->options(
                                    $events->mapWithKeys(fn (Event $event) => [$event->id => $event->name_date])
                                )
                        ]),
                    Block::make('member_non_event')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\Select::make('member_non_event')
                                ->hintIcon('heroicon-o-calendar')
                                ->label(__('Event'))
                                ->hintIcon('heroicon-o-calendar')
                                ->searchable()
                                ->options(
                                    $events->mapWithKeys(fn (Event $event) => [$event->id => $event->name_date])
                                )
                        ]),
                    Block::make('range_to_destination')
                        ->icon('heroicon-o-x-circle')
                        ->schema([
                            Forms\Components\TextInput::make('value')
                                ->label(__('Range to destination'))
                                ->hintIcon('heroicon-o-x-circle')
                                ->numeric()
                                ->step(5)
                                ->suffix('NM')
                                ->minLength(0)
                                ->maxLength(1000)
                                ->required()
                        ]),
                ]),
        ]);
    }
}
