<?php

namespace App\Filament\Resources;

use App\Enums\OperationType;
use App\Filament\Resources\WarehouseOperationResource\Pages;
use App\Filament\Resources\WarehouseOperationResource\RelationManagers;
use App\Models\WarehouseItem;
use App\Models\WarehouseOperation;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class WarehouseOperationResource extends Resource
{
    protected static ?string $model = WarehouseOperation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Склад';
    protected static ?string $modelLabel = 'приход/расход';
    protected static ?string $pluralModelLabel = 'приход/расход';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('item_id')
                    ->label(__('Товар'))
                    ->options(function () {
                        $result = DB::select("SELECT warehouse_items.id id, ( case when warehouse_items.category_id is null then concat( warehouse_items.code,' - ', warehouse_items.`name`) else concat( warehouse_items.code,' - ', warehouse_item_categories.`name`,' → ', warehouse_items.`name`) end ) name FROM warehouse_items LEFT JOIN warehouse_item_categories on warehouse_item_categories.id = warehouse_items.category_id");
                        return collect($result)->pluck('name', 'id');
                    })
                    ->searchable()
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
                        collect(OperationType::cases())->mapWithKeys(fn (OperationType $case, $key) => [$case->value => $case->label()])
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
                Textarea::make('comment')
                    ->label(__('Примечание'))
                    ->maxLength(160)
                    ->columnSpanFull(),
                Hidden::make('created_by')->default(auth()->user()->id)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('Товар')),
                Tables\Columns\BadgeColumn::make('operation')
                    ->label(__('Тип'))
                    ->formatStateUsing(fn ($state) => OperationType::from($state)->label())
                    ->colors([
                        'success' => OperationType::ADD->value,
                        'danger' => OperationType::SUBTRACT->value,
                    ]),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Количество'))
                    ->alignRight()
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                TextColumn::make('price')
                    ->label(__('Цена'))
                    ->alignRight()
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                TextColumn::make('total')
                    ->label(__('Сумма'))
                    ->alignRight()
                    ->getStateUsing(fn (?WarehouseOperation $record) => $record->amount * $record->price)
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                TextColumn::make('created_at')
                    ->label(__('Создан в'))
                    ->description(fn (WarehouseOperation $record) => $record->creator->name)
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state->format('d.m.Y')),
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWarehouseOperations::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $selectStatement = <<<SQL
            *, 
            case operation when 0 then amount else null end as debet, 
            case operation when 1 then amount else null end as credit,
           ( SELECT case when i.category_id is null then i.name else CONCAT(c.name,' → ', i.name) end full_name from warehouse_item_batches b left join warehouse_items i on b.warehouse_item_id = i.id left JOIN warehouse_item_categories c on i.category_id = c.id where b.id = warehouse_operations.warehouse_item_batch_id ) full_name
        SQL;
        return parent::getEloquentQuery()->withoutGlobalScopes()->with('creator')->selectRaw($selectStatement);
    }
}
