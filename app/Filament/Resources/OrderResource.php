<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\PaperProp;
use App\Models\PaperType;
use App\Models\PrintingForm;
use App\Models\ProfitPercentage;
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
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Layout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\TemporaryUploadedFile;

use function PHPUnit\Framework\callback;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';
    protected static ?string $navigationGroup = 'Работа';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'заказ';
    protected static ?string $pluralModelLabel = 'заказы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)->schema([
                    TextInput::make('reg_number')
                        ->label('Рег. номер')
                        ->prefix('#')
                        ->disabled()
                        ->visibleOn(['edit', 'view'])
                ]),
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
                                ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                    if ($get('state') > 0) {
                                        $set(
                                            'tirage_forecast',
                                            floor((float)$get('order_amount') / (float)$state)
                                        );
                                    }
                                    $set('total_amount', (int)$get('tirage') * (int)$state);
                                })
                                ->required(),
                            TextInput::make('order_amount')->type('number')
                                ->label(__('Заказ штук'))
                                ->minValue(0)
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                    if ($get('amount_per_paper') > 0) {
                                        $set(
                                            'tirage_forecast',
                                            floor((float)$state / (float)$get('amount_per_paper'))
                                        );

                                        $set('tirage', floor((float)$state / (float)$get('amount_per_paper')));
                                        $set('total_amount', (int)$get('amount_per_paper') * (int)$get('tirage'));
                                        $set('total_tirage',  (int)$get('tirage') + (int)$get('additional_tirage'));
                                    }
                                })
                                ->required(),
                            TextInput::make('tirage')->type('number')
                                ->label(__('Тираж'))
                                ->minValue(0)
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                    $set('total_amount', (int)$get('amount_per_paper') * (int)$state);
                                    $set('total_tirage',  (int)$state + (int)$get('additional_tirage'));
                                })
                                ->required(),
                            TextInput::make('additional_tirage')->type('number')
                                ->label(__('Дополнительный тираж'))
                                ->minValue(0)
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                    $set('total_tirage',  (int)$state + (int)$get('tirage'));
                                })
                                ->required(),
                            Card::make([
                                TextInput::make('total_amount')
                                    ->reactive()
                                    ->hidden(),
                                Placeholder::make('total_amount_label')
                                    ->label('Всего штук')
                                    ->content(function (callable $get) {
                                        return number_format($get('total_amount'), 0, ',', ' ');
                                    })
                                    ->reactive(),
                                TextInput::make('tirage_forecast')
                                    ->reactive()
                                    ->hidden(),
                                Placeholder::make('tirage_forecast_label')->label('Прогноз тираж')->content(function (Closure $get) {
                                    return number_format($get('tirage_forecast'), 0, ',', ' ');
                                }),
                                TextInput::make('total_tirage')
                                    ->reactive()
                                    ->hidden(),
                                Placeholder::make('total_tirage_label')
                                    ->label('Всего тираж')
                                    ->content(fn (Closure $get) => number_format($get('total_tirage'), 0, ',', ' ')),
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
                                ->columns(2),
                            Radio::make('profit_percentage')
                                ->options(
                                    fn () => ProfitPercentage::selectRaw("id, CONCAT(percentage,'%') as perc")->get()->pluck('perc', 'id')
                                )
                                ->label(__('.'))
                                ->reactive()
                                ->required(),
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
                        DatePicker::make('created_at')->label(__('Дата'))->default(now())->required()->disabled(),
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
                                        ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{998}000000000'))
                                        ->required(),
                                    TextInput::make('contacts.phone_secondary')
                                        ->mask(fn (TextInput\Mask $mask) => $mask->pattern('+{998}000000000'))
                                        ->label(__('Дополнительный номер телефона')),
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
                            ->reactive()
                            ->content(function (callable $get, callable $set) {
                                $paperPrice = $get('size') ? (int) PaperProp::select('price')->find($get('size'))->price : 0;
                                $servicesPrice =  0;
                                $tirage = (int)$get('tirage');
                                $totalAmount = (int)$get('order_amount') * $tirage;
                                $totalTirage =  $tirage + (int)$get('additional_tirage');
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


                                $result = $totalTirage == 0 || empty($get('services')) ?
                                    0 : ($totalTirage > 0 ? $totalTirage * $paperPrice : $paperPrice)
                                    + ($tirage < 1000 ? $servicesPrice : $servicesPrice * $totalTirage);
                                if ($get('profit_percentage')) {
                                    $profitPercentage = ProfitPercentage::findOrFail($get('profit_percentage'))->percentage;
                                    $profit = ($result / 100) * $profitPercentage;
                                    $result += $profit;
                                    $result += ($formPrices + $cutterPrice);
                                } else {
                                    $set('per_piece', 0);
                                    return new HtmlString("<span class=\"font-mono text-xl\">0</span>");
                                }
                                if ($get('total_amount') > 0) {
                                    $set('per_piece', $result / $get('total_amount'));
                                }
                                $result = number_format(ceil($result), 0, ', ', ' ');
                                return new HtmlString("<span class=\"font-mono text-xl\">{$result}</span>");
                            }),
                        TextInput::make('per_piece')
                            ->default(0)
                            ->hidden(),
                        Placeholder::make('per_piece_label')->id('per_piece')
                            ->content(function (callable $get) {
                                $result = number_format(ceil($get('per_piece')), 0, ', ', ' ');
                                return new HtmlString("<span class=\"font-mono text-xl\">{$result}</span>");
                            })
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
                TextColumn::make('reg_number')
                    ->label(__('Рег. номер'))
                    ->extraAttributes(['class' => 'font-mono']),
                TextColumn::make('item_name')
                    ->label(__('Назваиние товара')),
                TextColumn::make('amount_per_paper')
                    ->label(__('Штук за лист')),
                TextColumn::make('tirage')
                    ->label(__('Тираж')),
                BadgeColumn::make('status')
                    ->formatStateUsing(
                        fn (int $state) => __(OrderStatus::from($state)->label())
                    )
                    ->label(__('Статус'))
                    ->colors(OrderStatus::colors()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(
                        collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $status) => [$status->value => $status->label()])->toArray()
                    )
                    ->multiple()
                    ->label(__('Статус'))
                    ->default([OrderStatus::NEW->value])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filtersLayout(Layout::AboveContentCollapsible);
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
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
