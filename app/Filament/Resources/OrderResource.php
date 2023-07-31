<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\PaperProp;
use App\Models\PaperType;
use App\Models\PrintingForm;
use App\Models\Service;
use App\Models\ServicePrice;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Livewire\TemporaryUploadedFile;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';
    protected static ?string $navigationGroup = 'Работа';

    protected static ?string $modelLabel = 'заказ';
    protected static ?string $pluralModelLabel = 'заказы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Card::make([
                        TextInput::make('item_name')->label(__('Название товара'))
                            ->placeholder(__('Название товара'))
                            ->required()
                            ->columnSpanFull(),

                        // Paper
                        Fieldset::make(_('Бумага'))->schema([
                            Select::make('paper_type')
                                ->label(__('Тип бумаги'))
                                ->options(fn () => PaperType::query()->select('id', 'name')->get()->pluck('name', 'id'))
                                ->columnSpanFull()
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('grammage', null))
                                ->required(),
                            Select::make('grammage')
                                ->label(__('Граммаж'))
                                ->options(function (callable $get) {
                                    $paperTypeId = $get('paper_type');
                                    if (!$paperTypeId) {
                                        return [];
                                    }
                                    return PaperProp::select('grammage')
                                        ->where('paper_type_id', $paperTypeId)
                                        ->groupBy('grammage')
                                        ->get()
                                        ->pluck('grammage', 'grammage');
                                })
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn (callable $set) => $set('size', null))
                                ->disabled(fn (callable $get) => !(bool) $get('paper_type')),
                            Select::make('size')
                                ->label(__('Размер'))
                                ->reactive()
                                ->options(function (callable $get) {
                                    $grammage = $get('grammage');
                                    $paperTypeId = $get('paper_type');

                                    if (!$grammage) {
                                        return [];
                                    }
                                    return PaperProp::select('id', 'size')->where('grammage', $grammage)->where('paper_type_id', $paperTypeId)->get()->pluck('size', 'id');
                                })
                                ->disabled(fn (callable $get) => !(bool) $get('grammage'))
                                ->required(),
                        ]),

                        // Piece and tirage
                        Fieldset::make(__('Штук и тираж'))->schema([
                            TextInput::make('amount_per_paper')->type('number')
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
                                Placeholder::make('total_amount')->label('Всего штук')->content(fn ($get) => number_format((int)$get('amount_per_paper') * (int)$get('tirage'), 0, ',', ' ')),
                                Placeholder::make('tirage_forecast')->label('Прогноз тираж')->content(function (Closure $get) {
                                    if ($get('order_amount') && $get('amount_per_paper')) {
                                        return number_format(floor((float)$get('order_amount') / (float)$get('amount_per_paper')), 0, ',', ' ');
                                    }
                                    return 0;
                                }),
                                Placeholder::make('total_tirage')
                                    ->label('Всего тираж')
                                    ->content(fn ($get) => number_format((int)$get('tirage') + (int)$get('additional_tirage'), 0, ',', ' ')),
                            ])->columns(3)
                        ]),
                        Fieldset::make(__('Услуги и формы'))->schema([
                            Radio::make('print_type')
                                ->label(__('Краска'))
                                ->options([
                                    '4+0' => '4+0',
                                    '4+4' => '4+4',
                                ])
                                ->required()
                                ->reactive()
                                ->default('4+0')
                                ->inline()
                                ->columnSpanFull(),
                            CheckboxList::make('services')
                                ->id('services')
                                ->label(__('Услуги'))
                                ->options(fn () => Service::query()->select('id', 'name')->get()->pluck('name', 'id'))
                                ->required()
                                ->reactive()
                                ->columns(2)->columnSpanFull(),
                            CheckboxList::make('printing_forms')
                                ->id('printing_forms')
                                ->label(__('Печатные Формы'))
                                ->reactive()
                                ->options(fn () => PrintingForm::query()->select('id', 'name')->whereNot('name', 'like', '%Пичок%')->get()->pluck('name', 'id'))
                                ->columns(2)
                                ->columnSpanFull(),
                            Select::make('cutter')
                                ->label(__('Пичок'))
                                ->reactive()
                                ->options(fn () => PrintingForm::select('id', 'name')->where('name', 'like', '%Пичок%')->get()->pluck('name', 'id'))
                        ])
                    ])->columns(2),
                ])->columnSpan(2),

                // Side panel
                Grid::make(1)->schema([
                    Card::make([
                        DatePicker::make('created_at')->label(__('Дата'))->default(now())->required(),
                        Select::make('client_id')
                            ->searchable(true)
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->label(__('Наименование клиента'))
                                    ->required(),
                                TextInput::make('rep_name')->label(__('Контактное лицо'))
                                    ->required(),
                                Fieldset::make('contacts')->schema([
                                    TextInput::make('contacts.phone_primary')->label(__('Основной номер телефона'))
                                        ->required(),
                                    TextInput::make('contacts.phone_secondary')->label(__('Дополнительный номер телефона')),
                                ])->label('Контактная информация')
                            ])
                            ->preload()
                            ->disablePlaceholderSelection()
                            ->relationship('client', 'name')
                            ->label('Клиент'),
                    ]),
                    Card::make([
                        FileUpload::make('item_image')
                            ->required()
                            ->label(__('Изображение'))
                            ->directory('order-images')
                            ->image(),
                    ]),
                    Card::make([
                        Placeholder::make('total')
                            ->label(__('Всего к оплате'))
                            ->content(function (callable $get, callable $set) {
                                $paperPrice = $get('size') ? (int) PaperProp::select('price')->find($get('size'))->price : 0;
                                $servicesPrice =  0;
                                $tirage = (int)$get('tirage');
                                $totalAmount = (int)$get('order_amount') * $tirage;
                                $totalTirage =  $tirage + (int)$get('additional_tirage');
                                $set('perPiece', 'sava');
                                $formPrices = empty($get('printing_forms')) ? 0 :  PrintingForm::query()
                                    ->when(
                                        $get('print_type') == '4+4',
                                        fn (Builder $query) => $query->select('double_four_price as price'),
                                        fn (Builder $query) => $query->select('four_zero_price as price')
                                    )->whereIn('id', $get('printing_forms'))
                                    ->get()
                                    ->sum('price');
                                $cutterPrice = $get('cutter') ? PrintingForm::query()
                                    ->when(
                                        $get('print_type') == '4+4',
                                        fn (Builder $query) => $query->select('double_four_price as price'),
                                        fn (Builder $query) => $query->select('four_zero_price as price')
                                    )->find($get('cutter'))->price : 0;
                                if (!empty($get('services'))) {
                                    $servicesPrice = ServicePrice::query()
                                        ->whereIn('service_id', $get('services'))
                                        ->where('print_type', $get('print_type'))
                                        ->when(
                                            $tirage < 1000,
                                            fn (Builder $query) => $query->select('price_before_1k as price'),
                                            fn (Builder $query) => $query->select('price_after_1k as price')
                                        )->get()->sum('price');
                                }
                                return $totalTirage == 0 || empty($get('services')) ?
                                    0 : ($totalTirage > 0 ? $totalTirage * $paperPrice : $paperPrice)
                                    + ($tirage < 1000 ? $servicesPrice : $servicesPrice * $totalTirage)
                                    + ($get('print_type') == '4+4' ? 300000 : 200000)
                                    + ($formPrices + $cutterPrice);
                            })->reactive(),
                        Placeholder::make('perPiece')->id('per_piece')
                            ->reactive()
                            ->label('Цена за штуку')
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
