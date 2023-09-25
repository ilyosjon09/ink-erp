<?php

namespace App\Filament\Resources\WarehouseItemResource\RelationManagers;

use App\Enums\WarehouseOperationType;
use App\Models\WarehouseOperation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OperationsRelationManager extends RelationManager
{
    protected static string $relationship = 'operations';
    protected static ?string $recordTitleAttribute = 'operation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('operation')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('operation')
                    ->label(__('Тип'))
                    ->formatStateUsing(fn ($state) => WarehouseOperationType::from($state)->label())
                    ->colors([
                        'success' => WarehouseOperationType::ADD->value,
                        'danger' => WarehouseOperationType::SUBTRACT->value,
                    ]),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Количество'))
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('Цена'))
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('Сумма'))
                    ->getStateUsing(fn (?WarehouseOperation $record) => $record->amount * $record->price)
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ')),
            ])
            ->filters([
                //
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
