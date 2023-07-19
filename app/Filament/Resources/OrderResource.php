<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?string $modelLabel = 'заказ';
    protected static ?string $pluralModelLabel = 'заказы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Card::make([
                        TextInput::make('item_name')->label(__('Название товара'))
                            ->placeholder(__('Название товара'))->required()->columnSpanFull(),
                        Fieldset::make(_('Бумага'))->schema([
                            Select::make('paper_type')
                                ->label(__('Тип бумаги'))
                                ->options([
                                    'draft' => 'Draft',
                                    'reviewing' => 'Reviewing',
                                    'published' => 'Published',
                                ])
                                ->columnSpanFull()
                                ->required(),
                            Select::make('grammage')
                                ->label(__('Граммаж'))
                                ->disabled(fn (Closure $get) => $get('paper_type') === null)
                                ->required(),
                            Select::make('size')
                                ->label(__('Размер'))
                                ->disabled(fn (Closure $get) => $get('grammage') === null)
                                ->required(),
                        ]),
                        Fieldset::make(__('Штук и тираж'))->schema([
                            TextInput::make('amount_per_page')->type('number')
                                ->label(__('Штук на листе'))
                                ->minValue(0)
                                ->reactive()
                                ->required(),
                            TextInput::make('order_amount')->type('number')
                                ->label(__('Заказ штук'))
                                ->minValue(0)
                                ->reactive()
                                ->required(),
                            TextInput::make('tirage')->type('number')
                                ->label(__('Тираж'))
                                ->minValue(0)
                                ->reactive()
                                ->required(),
                            TextInput::make('additional_tirage')->type('number')
                                ->label(__('Дополнительный тираж'))
                                ->minValue(0)
                                ->reactive()
                                ->required(),
                            Card::make([
                                Placeholder::make('total_amount')->label('Всего штук')->content(fn ($get) => $get('amount_per_page') * $get('tirage')),
                                Placeholder::make('tirage_forecast')->label('Прогноз тираж')->content(function (Closure $get) {
                                    if ($get('order_amount') && $get('amount_per_page')) {
                                        return floor($get('order_amount') / $get('amount_per_page'));
                                    }
                                    return 0;
                                }),
                                Placeholder::make('total_tirage')->label('Всего тираж')->content(fn ($get) => $get('tirage') + $get('additional_tirage')),
                            ])->columns(3)
                        ]),
                        Fieldset::make(__('Услуги и формы'))->schema([
                            Radio::make('print_type')
                                ->label(__('Краска'))
                                ->options([
                                    'four_zero' => '4+0',
                                    'four_four' => '4+4',
                                ])
                                ->required()
                                ->default('four_zero')
                                ->inline()
                                ->columnSpanFull(),
                            CheckboxList::make('services')
                                ->label(__('Услуги'))
                                ->options([
                                    'Печать',
                                    'Лак',
                                    'Лак офсет',
                                    'Ламинация Мат',
                                    'Ламинация Глянц.',
                                    'Выбороч. Лак',
                                    'Тигель',
                                    'Резка',
                                    'Тиснение',
                                    'Склейка',
                                ])->required(),
                            Grid::make(1)->schema([
                                CheckboxList::make('printing_forms')
                                    ->label(__('Формы'))
                                    ->options([
                                        'СТП',
                                        'Выб колиб',
                                        'Клище',
                                    ])->required(),
                                Radio::make('cutter_size')
                                    ->label(__('Пичок'))
                                    ->options([
                                        'A2',
                                        'A3',
                                        'A4',
                                        'A5',
                                    ])
                                    ->required()
                                    ->default('four_zero')
                                    ->inline()
                            ])->columnSpan(1)
                        ])
                    ])->columns(2),
                ])->columnSpan(2),

                // Side panel
                Grid::make(1)->schema([
                    Card::make([
                        DatePicker::make('date')->label(__('Дата'))->default(now())->required(),
                        Select::make('client_id')
                            ->searchable(true)
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->label(__('Наименование клиента'))
                                    ->required(),
                                TextInput::make('rep_name')->label(__('Контактное лицо'))
                                    ->required(),
                                Fieldset::make('contacts')->schema([
                                    TextInput::make('phone_primary')->label(__('Основной номер телефона'))
                                        ->required(),
                                    TextInput::make('phone_secondary')->label(__('Дополнительный номер телефона')),
                                ])->label('Контактная информация')
                            ])
                            ->preload()
                            ->relationship('client', 'name')
                            ->label('Клиент'),
                    ]),
                    Card::make([
                        FileUpload::make('item_image')->label(__('Изображение')),
                    ]),
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
