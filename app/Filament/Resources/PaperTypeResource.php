<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaperTypeResource\Pages;
use App\Filament\Resources\PaperTypeResource\RelationManagers;
use App\Filament\Resources\PaperTypeResource\RelationManagers\PropertiesRelationManager;
use App\Models\PaperType;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaperTypeResource extends Resource
{
    protected static ?string $model = PaperType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Справочники';

    protected static ?string $modelLabel = 'тип бумаги';
    protected static ?string $pluralModelLabel = 'типы бумаги';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')->label(__('Тип'))->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Тип')),
                TextColumn::make('properties_count')->label(__('Количество цен'))
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
            PropertiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaperTypes::route('/'),
            'create' => Pages\CreatePaperType::route('/create'),
            'edit' => Pages\EditPaperType::route('/{record}/edit'),
        ];
    }
}
