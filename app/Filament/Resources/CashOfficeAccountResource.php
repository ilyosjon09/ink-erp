<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashOfficeAccountResource\Pages;
use App\Filament\Resources\CashOfficeAccountResource\RelationManagers;
use App\Models\CashOfficeAccount;
use App\Enums\CashOfficeAccountType;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CashOfficeAccountResource extends Resource
{
    protected static ?string $model = CashOfficeAccount::class;

    protected static ?string $navigationGroup = 'Касса';
    protected static ?string $modelLabel = 'счет';
    protected static ?string $pluralModelLabel = 'счета';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('number')
                        ->label(__('Название/номер счета'))
                        ->unique()
                        ->required(),
                    Select::make('type')
                        ->label(__('Тип'))
                        ->options(
                            fn () => collect(CashOfficeAccountType::cases())
                                ->filter(fn (CashOfficeAccountType $case) => $case->value !== 3)
                                ->mapWithKeys(fn (CashOfficeAccountType $case) => [$case->value => $case->label()])
                        )
                        ->required()
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label(__('Название/номер счета')),
                TextColumn::make('Тип')
                    ->formatStateUsing(fn ($state) => CashOfficeAccountType::from($state)->label()),
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
            'index' => Pages\ListCashOfficeAccounts::route('/'),
            'create' => Pages\CreateCashOfficeAccount::route('/create'),
            'edit' => Pages\EditCashOfficeAccount::route('/{record}/edit'),
        ];
    }
}
