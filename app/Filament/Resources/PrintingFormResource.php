<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrintingFormResource\Pages;
use App\Filament\Resources\PrintingFormResource\RelationManagers;
use App\Models\PrintingForm;
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

class PrintingFormResource extends Resource
{
    protected static ?string $model = PrintingForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Справочники';

    protected static ?string $modelLabel = 'печатная форма';
    protected static ?string $pluralModelLabel = 'печатние формы';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')
                        ->label(__('Наименование'))
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('four_zero_price')
                        ->required()
                        ->label(__('Цена за 4+0')),
                    TextInput::make('double_four_price')
                        ->required()
                        ->label(__('Цена за 4+4')),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Наименование')),
                TextColumn::make('four_zero_price')
                    ->label(__('Цена за 4+0')),
                TextColumn::make('double_four_price')
                    ->label(__('Цена за 4+4')),
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
            'index' => Pages\ListPrintingForms::route('/'),
            'create' => Pages\CreatePrintingForm::route('/create'),
            'edit' => Pages\EditPrintingForm::route('/{record}/edit'),
        ];
    }
}
