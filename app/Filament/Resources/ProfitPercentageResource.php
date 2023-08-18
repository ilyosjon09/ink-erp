<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfitPercentageResource\Pages;
use App\Filament\Resources\ProfitPercentageResource\RelationManagers;
use App\Models\ProfitPercentage;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfitPercentageResource extends Resource
{
    protected static ?string $model = ProfitPercentage::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-tax';
    protected static ?string $navigationGroup = 'Справочники';

    protected static ?string $modelLabel = 'прибыл';
    protected static ?string $pluralModelLabel = 'прибыль';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('percentage')
                    ->label('Процент')
                    ->placeholder('Процент')
                    ->type('number')
                    ->minValue(1)
                    ->maxValue(100)
                    ->suffix('%')
                    ->required()
                    ->autofocus()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('percentage')->label('Процент')->suffix('%')->sortable()
            ])->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProfitPercentages::route('/'),
        ];
    }
}
