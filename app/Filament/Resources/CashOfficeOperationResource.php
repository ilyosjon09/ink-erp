<?php

namespace App\Filament\Resources;

use App\Enums\OperationType;
use App\Filament\Resources\CashOfficeOperationResource\Pages;
use App\Filament\Resources\CashOfficeOperationResource\RelationManagers;
use App\Models\CashOfficeOperation;
use App\Models\CashOfficeOperationTemplate;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashOfficeOperationResource extends Resource
{
    protected static ?string $model = CashOfficeOperation::class;

    protected static ?string $navigationGroup = 'Касса';
    protected static ?string $modelLabel = 'приход/расход';
    protected static ?string $pluralModelLabel = 'приход/расход';

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cash_office_operation_template_id')
                    ->searchable()
                    ->label(__('Шаблон'))
                    ->relationship('cashOfficeOperationTemplate', 'name')
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('Название'))
                            ->unique()
                            ->required(),
                        Radio::make('operation')
                            ->label(__('Тип'))
                            ->options(
                                collect(OperationType::cases())->mapWithKeys(fn (OperationType $case, $key) => [$case->value => $case->label()])
                            )
                            ->required()
                            ->columnSpanFull(),
                        Select::make('cash_office_account_id')
                            ->label(__('Счет'))
                            ->relationship('cashOfficeAccount', 'number')
                            ->preload()
                            ->required(),
                        Textarea::make('purpose')
                            ->label(__('Цель операции'))
                            ->required()
                    ])
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $template = CashOfficeOperationTemplate::find($state);

                        $set('operation', $template->operation);
                        $set('cash_office_account_id', $template->cash_office_account_id);
                        $set('purpose', $template->purpose);
                    }),
                Select::make('cash_office_account_id')
                    ->label(__('Счет'))
                    ->relationship('cashOfficeAccount', 'number')
                    ->preload()
                    ->required(),
                Radio::make('operation')
                    ->label(__('Тип'))
                    ->options(
                        collect(OperationType::cases())->mapWithKeys(fn (OperationType $case, $key) => [$case->value => $case->label()])
                    )
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('amount')
                    ->label(__('Сумма'))
                    ->numeric()
                    ->type('number')
                    ->required(),
                Textarea::make('purpose')
                    ->label(__('Цель операции'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('Дата'))
                    ->formatStateUsing(fn ($state) => $state->format('d.m.Y H:i')),
                TextColumn::make('cashOfficeAccount.number')
                    ->label(__('Счет')),
                Tables\Columns\BadgeColumn::make('operation')
                    ->label(__('Тип'))
                    ->formatStateUsing(fn ($state) => OperationType::from($state)->label())
                    ->colors([
                        'success' => OperationType::ADD->value,
                        'danger' => OperationType::SUBTRACT->value,
                    ]),
                TextColumn::make('amount')
                    ->label(__('Сумма'))
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
            'index' => Pages\ManageCashOfficeOperations::route('/'),
        ];
    }
}
