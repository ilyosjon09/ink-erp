<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseItemCategoryResource\Pages;
use App\Models\PaperProp;
use App\Models\PaperType;
use App\Models\PrintingForm;
use App\Models\WarehouseItemCategory;
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
                Grid::make(
                    1
                )->schema([
                    Select::make('bindable_type')
                        ->label(__('Связать с'))
                        ->options([
                            PaperType::class => __('📄 Бумага'),
                            PrintingForm::class => __('🖨️ Печатьние формы')
                        ])->reactive(),
                    Select::make('bindable_id')
                        ->reactive()
                        ->required(fn (callable $get) => $get('bindable_type') === PaperType::class)
                        ->label(__('Тип'))
                        ->options(fn () => PaperType::query()->select(['id', 'name'])->get()->mapWithKeys(fn ($paperType) => [$paperType->id => $paperType->name]))
                        ->hidden(fn (callable $get) => $get('bindable_type') !== PaperType::class)
                        ->afterStateUpdated(callback: fn ($state, callable $set) => $set('name', PaperType::query()->findOrFail((int)$state)->name)),
                    Select::make('bindable_id')
                        ->required(fn (callable $get) => $get('bindable_type') === PrintingForm::class)
                        ->label(__('Форма'))
                        ->reactive()
                        ->options(fn () => PrintingForm::query()->select(['id', 'name'])->get()->mapWithKeys(fn ($paperType) => [$paperType->id => $paperType->name]))
                        ->hidden(fn (callable $get) => $get('bindable_type') !== PrintingForm::class)
                        ->afterStateUpdated(callback: fn ($state, callable $set) => $set('name', PrintingForm::query()->findOrFail((int)$state)->name)),
                    TextInput::make('name')
                        ->label(__('Название'))
                        ->disabled(fn (callable $get) => $get('bindable_id'))
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
