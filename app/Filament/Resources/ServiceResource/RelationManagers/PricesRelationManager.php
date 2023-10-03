<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Filters\TernaryFilter;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $recordTitleAttribute = 'print_type';

    protected static ?string $modelLabel = 'цена';
    protected static ?string $pluralModelLabel = 'цены';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('price_before_1k')
                    ->label(__('Цена до 1000'))
                    ->type('number')
                    ->minValue(0)
                    ->required(),
                TextInput::make('price_after_1k')
                    ->label(__('Цена после 1000'))
                    ->type('number')
                    ->minValue(0)
                    ->required(),
                Fieldset::make(__('Параметры'))
                    ->schema([
                        Grid::make(1)->schema([
                            Radio::make('print_type')
                                ->label(__('Краска'))
                                ->options([
                                    '4+0' => '4+0',
                                    '4+4' => '4+4',
                                ])
                                ->required()
                                ->inline()
                                ->default('4+0'),
                            Radio::make('calc_method')
                                ->label(__('Способ расчета'))
                                ->options([
                                    'за тираж',
                                    'за штуку',
                                ])
                                ->required()
                                ->inline()
                                ->default(0)
                                ->helperText(__('Подсчитать для каждого тиража или для каждого листа бумаги?')),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price_before_1k')
                    ->formatStateUsing(fn ($state): string => $state ? number_format($state, 0, ',', ' ') : '')
                    ->label(__('Цена до 1000')),
                Tables\Columns\TextColumn::make('price_after_1k')
                    ->formatStateUsing(fn ($state): string => $state ? number_format($state, 0, ',', ' ') : '')
                    ->label(__('Цена после 1000')),
                Tables\Columns\TextColumn::make('print_type')
                    ->label(__('Краска')),
                Tables\Columns\TextColumn::make('calc_method')
                    ->enum([
                        'за тираж',
                        'за штуку',
                    ])
                    ->label(__('Способ расчета')),
            ])
            ->filters([
                TernaryFilter::make('print_type')
                    ->placeholder('Все')
                    ->trueLabel('4+0')
                    ->falseLabel('4+4')
                    ->queries(
                        true: fn (Builder $query) => $query->where('print_type', '4+4'),
                        false: fn (Builder $query) => $query->where('print_type', '4+0'),
                        blank: fn (Builder $query) => $query,
                    )->label(__('Краска'))
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
