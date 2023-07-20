<?php

namespace App\Filament\Resources\PaperTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    protected static ?string $modelLabel = 'цена';
    protected static ?string $pluralModelLabel = 'цены';

    protected static ?string $recordTitleAttribute = 'print_method';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('grammage')
                    ->label(__('Граммаж'))
                    ->numeric()
                    ->required(),
                TextInput::make('size')
                    ->label(__('Размер'))
                    ->required(),
                TextInput::make('price')
                    ->label(__('Цена'))
                    ->numeric()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grammage')
                    ->label(__('Граммаж')),
                Tables\Columns\TextColumn::make('size')
                    ->label(__('Размер')),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('Цена')),
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
