<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Справочники';

    protected static ?string $modelLabel = 'клиент';
    protected static ?string $pluralModelLabel = 'клиенты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')->label(__('Наименование клиента'))
                        ->required(),
                    TextInput::make('rep_name')->label(__('Контактное лицо'))
                        ->required(),
                    Fieldset::make('contacts')->schema([
                        TextInput::make('contacts.phone_primary')->label(__('Основной номер телефона'))
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{998}000000000'))
                            ->required(),
                        TextInput::make('contacts.phone_secondary')
                            ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{998}000000000'))
                            ->label(__('Дополнительный номер телефона')),
                    ])->label('Контактная информация')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Назание')),
                TextColumn::make('rep_name')
                    ->label(__('Контактое лицо')),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
