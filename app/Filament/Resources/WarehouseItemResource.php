<?php

namespace App\Filament\Resources;

use App\Enums\WarehouseOperationType;
use App\Filament\Resources\WarehouseItemResource\Pages;
use App\Filament\Resources\WarehouseItemResource\RelationManagers;
use App\Filament\Resources\WarehouseItemResource\RelationManagers\OperationsRelationManager;
use App\Models\PaperProp;
use App\Models\PrintingForm;
use App\Models\WarehouseItem;
use App\Models\WarehouseItemCategory;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                            ->columnSpan(1),
                        Select::make('association_type')
                            ->label(__('Связать с'))
                            ->options([
                                PaperProp::class => '📄 Тип бумаги',
                                PrintingForm::class => '🖨️ Печатние формы',
                            ])->reactive(),
                        Select::make('association_id')
                            ->label(__('Бумаги'))
                            ->searchable()
                            ->reactive()
                            ->visible(fn (callable $get) => !is_null($get('association_type')))
                            ->options(
                                function () {
                                    $props = PaperProp::query()->with('paperType')->select('id', 'grammage', 'paper_type_id', 'divided_into', 'size')->get();
                                    return $props->mapWithKeys(fn (PaperProp $prop) => [$prop->id => Str::swap(
                                        [
                                            ':name' =>  $prop->paperType->name,
                                            ':grammage' =>  $prop->grammage,
                                            ':size' =>  $prop->size,
                                            ':divided_into' =>  $prop->divided_into,
                                        ],
                                        "📄 :name › :grammageгр. › :size (1/:divided_into)"
                                    )]);
                                }
                            )->reactive(),
                        TextInput::make('code')
                            ->label(__('Код'))
                            ->placeholder('000')
                            ->autofocus()
                            ->unique()
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
                        Hidden::make('created_by')->default(auth()->user()->id),
                        Grid::make(3)->schema([
                            Card::make([
                                Placeholder::make('add')
                                    ->label(__('Приход'))
                                    ->content(fn (?Model $record) => $record->operations->where('operation', WarehouseOperationType::ADD)->sum('amount'))
                            ])->columnSpan(1),
                            Card::make([
                                Placeholder::make('subtract')
                                    ->label(__('Расход'))
                                    ->content(fn (?Model $record) => $record->operations->where('operation', WarehouseOperationType::SUBTRACT)->sum('amount'))
                            ])->columnSpan(1),
                            Card::make([
                                Placeholder::make('subtract')
                                    ->label(__('Остаток'))
                                    ->content(fn (?Model $record) => $record->operations->where(
                                        'operation',
                                        WarehouseOperationType::ADD
                                    )->sum('amount') - $record->operations->where('operation', WarehouseOperationType::SUBTRACT)->sum('amount'))
                            ])->columnSpan(1),
                        ])->visibleOn('edit')
                    ],
                )
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('operations');
    }
}
