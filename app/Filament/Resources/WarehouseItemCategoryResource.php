<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseItemCategoryResource\Pages;
use App\Filament\Resources\WarehouseItemCategoryResource\RelationManagers;
use App\Models\PaperType;
use App\Models\WarehouseItemCategory;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseItemCategoryResource extends Resource
{
    protected static ?string $model = WarehouseItemCategory::class;
    protected static ?string $navigationGroup = 'Склад';
    protected static ?string $modelLabel = 'категория';
    protected static ?string $pluralModelLabel = 'категории';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('for_paper')
                    ->label('Привязать к типу бумаги')
                    ->inline(false)
                    ->columnSpanFull()
                    ->reactive(),
                Grid::make(
                    1
                )->schema([
                    Select::make('bindable_id'),
                    Select::make('bindable_type'),
                    Select::make('paper_type_id')
                        ->label(__('Тип бумаги'))
                        ->relationship('paperType', 'name')
                        ->preload()
                        ->hidden(fn (callable $get) => !$get('for_paper'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', PaperType::query()->findOrFail((int)$state)->name))
                        ->searchable(),
                    TextInput::make('name')
                        ->label(__('Название'))
                        ->disabled(fn (callable $get) => $get('for_paper'))
                        ->reactive()
                        ->unique()
                        ->required(),
                ])->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Название')),
                BadgeColumn::make('for_paper')
                    ->label(__('Связано с типом бумаги'))
                    ->formatStateUsing(fn ($state) => $state ? __('Да') : __('Нет'))
                    ->colors([
                        'success' => true
                    ]),
                TextColumn::make('items_count')
                    ->label(__('Количество товаров'))
                    ->counts('items'),
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
            'index' => Pages\ListWarehouseItemCategories::route('/'),
            'create' => Pages\CreateWarehouseItemCategory::route('/create'),
            'edit' => Pages\EditWarehouseItemCategory::route('/{record}/edit'),
        ];
    }
}
