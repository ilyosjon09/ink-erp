<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseItemResource\Pages;
use App\Filament\Resources\WarehouseItemResource\RelationManagers;
use App\Filament\Resources\WarehouseItemResource\RelationManagers\OperationsRelationManager;
use App\Models\PaperProp;
use App\Models\WarehouseItem;
use App\Models\WarehouseItemCategory;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseItemResource extends Resource
{
    protected static ?string $model = WarehouseItem::class;
    protected static ?string $navigationGroup = 'Склад';
    protected static ?string $modelLabel = 'товар';
    protected static ?string $pluralModelLabel = 'товары';
    protected static ?string $navigationIcon = 'heroicon-o-view-grid-add';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema(
                    [
                        Select::make('category_id')
                            ->label(__('Категория'))
                            ->relationship('category', 'name')
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(fn ($state) => $state ? (WarehouseItemCategory::query()->findOrFail($state)->for_paper ? 2 : 3) : 3),
                        Select::make('grammage')
                            ->label(__('Граммаж'))
                            ->visible(fn (callable $get) => $get('category_id') ? WarehouseItemCategory::query()->findOrFail($get('category_id'))->for_paper : false)
                            ->options(function (callable $get) {
                                $paperType = WarehouseItemCategory::query()->findOrFail($get('category_id'))->paper_type_id;
                                return PaperProp::query()->where('paper_type_id', $paperType)->select('grammage')->groupBy('grammage')->get()->pluck('grammage', 'grammage');
                            })
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set, $state) {
                                $category = WarehouseItemCategory::query()->findOrFail($get('category_id'));

                                if ($category->for_paper) {
                                    $set('name', $state);
                                }
                            })
                            ->preload(),
                        TextInput::make('code')
                            ->label(__('Код'))
                            ->placeholder('000')
                            ->autofocus()
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('000'))
                            ->required(),
                        TextInput::make('name')
                            ->label(__('Название'))
                            ->required()
                            ->columnSpan(2)
                            ->unique(),
                        TextInput::make('measurement_unit')
                            ->label(__('Единица измерение'))
                            ->datalist([
                                'шт.',
                                'л.',
                                'кг.',
                                'м.',
                            ])
                            ->columnSpanFull()
                            ->required(),
                        Hidden::make('created_by')->default(auth()->user()->id)
                    ]
                )->columnSpan(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label((__('Код'))),
                TextColumn::make('label')->label((__('Название')))->formatStateUsing(function (WarehouseItem $record) {
                    return $record->full_name ?? $record->name;
                }),
                TextColumn::make('balance')->label((__('Сальдо'))),
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
            OperationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouseItems::route('/'),
            'create' => Pages\CreateWarehouseItem::route('/create'),
            'edit' => Pages\EditWarehouseItem::route('/{record}/edit'),
        ];
    }
}
