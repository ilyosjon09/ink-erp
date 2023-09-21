<?php

namespace App\Filament\Resources;

use App\Enums\WarehouseOperationType;
use App\Filament\Resources\WarehouseOperationResource\Pages;
use App\Filament\Resources\WarehouseOperationResource\RelationManagers;
use App\Models\WarehouseOperation;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseOperationResource extends Resource
{
    protected static ?string $model = WarehouseOperation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Склад';
    protected static ?string $modelLabel = 'операция';
    protected static ?string $pluralModelLabel = 'складские операции';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                    ->label(__('Товар'))
                    ->relationship('item', 'name')
                    ->createOptionForm([
                        Grid::make(3)->schema(
                            [
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
                                    ->columnSpanFull()
                                    ->required(),
                                Hidden::make('created_by')->default(auth()->user()->id)
                            ]
                        )
                    ])
                    ->createOptionAction(function (Action $action) {
                        $action->modalWidth('md')->modalHeading(__('Создать товар'));
                    })
                    ->required()
                    ->preload(),
                Radio::make('operation')
                    ->label(__('Тип'))
                    ->options(
                        collect(WarehouseOperationType::cases())->mapWithKeys(fn (WarehouseOperationType $case, $key) => [$case->value => $case->label()])
                    )
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->label(__('Количество'))
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('price')
                    ->label(__('Цена'))
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                DatePicker::make('created_at')
                    ->label(__('Дата'))
                    ->default(now()),
                Hidden::make('created_by')->default(auth()->user()->id)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')
                    ->label(__('Товар')),
                BadgeColumn::make('operation')
                    ->label(__('Тип'))
                    ->formatStateUsing(fn ($state) => WarehouseOperationType::from($state)->label())
                    ->colors([
                        'success' => WarehouseOperationType::ADD->value,
                        'error' => WarehouseOperationType::SUBTRACT->value,
                    ]),
                TextColumn::make('amount')
                    ->label(__('Количество'))
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                TextColumn::make('price')
                    ->label(__('Цена'))
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                TextColumn::make('total')
                    ->label(__('Сумма'))
                    ->getStateUsing(fn (?WarehouseOperation $record) => $record->amount * $record->price)
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                TextColumn::make('creator.name')
                    ->label(__('Создатель')),
                TextColumn::make('created_at')
                    ->label(__('Создан в'))
                    ->formatStateUsing(fn ($state) => $state->format('d.m.Y H:i')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWarehouseOperations::route('/'),
        ];
    }
}
