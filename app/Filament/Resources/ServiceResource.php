<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Filament\Resources\ServiceResource\RelationManagers\PricesRelationManager;
use App\Models\Service;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';
    protected static ?string $navigationGroup = 'Справочники';

    protected static ?string $modelLabel = 'услуга';
    protected static ?string $pluralModelLabel = 'услуги';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Название'))
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('Название')),
                TextColumn::make('prices.price_before_1k')
                    ->formatStateUsing(fn (?Service $record) => Str::of($record->prices->reduce(fn ($prev, $curr) => $prev .= "{$curr->print_type}→{$curr->price_before_1k}, "))->rtrim(', '))
                    ->label(__('Цена до 1000')),
                TextColumn::make('prices.price_after_1k')
                    ->formatStateUsing(fn (?Service $record) => Str::of($record->prices->reduce(fn ($prev, $curr) => $prev .= "{$curr->print_type}→{$curr->price_after_1k}, "))->rtrim(', '))
                    ->label(__('Цена после 1000')),
                TextColumn::make('name')->label(__('Название')),
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
            PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
