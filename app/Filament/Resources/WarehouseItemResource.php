<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseItemResource\Pages;
use App\Filament\Resources\WarehouseItemResource\RelationManagers;
use App\Filament\Resources\WarehouseItemResource\RelationManagers\OperationsRelationManager;
use App\Models\WarehouseItem;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
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
                )->maxWidth('xl')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label((__('Код'))),
                TextColumn::make('name')->label((__('Название'))),
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
