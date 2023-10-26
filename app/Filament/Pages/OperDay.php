<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Widgets\OperDayOverview;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use App\Models\OperDay as OperDayModel;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class OperDay extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.oper-day';

    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Опердень';
    protected static ?string $slug = 'operday';
    protected static ?string $navigationLabel = 'Опердень';
    public OperDayModel $currentOperDay;
    public bool $closed;
    protected $listeners = ['refresh-operday-widget' => 'refreshOperDay'];

    public function mount(): void
    {
        $currentOperDay = OperDayModel::query()->whereIsCurrent(true)->first();

        $this->currentOperDay = $currentOperDay;
        $this->closed = $currentOperDay->closed;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OperDayOverview::class,
        ];
    }

    protected function getActions(): array
    {
        return [
            // Open operday
            Action::make('open-operday')
                ->label(__('Открыт опердень'))
                ->hidden(!$this->currentOperDay->closed)
                ->form([
                    DatePicker::make('operday')
                        ->withoutTime()
                        ->minDate($this->currentOperDay->operday->addDay())
                        ->default($this->currentOperDay->operday->addDay())
                        ->displayFormat('d.m.Y')
                        ->label(__('Опердень'))
                ])
                ->action(function ($data) {
                    $this->currentOperDay->refresh();
                    $this->currentOperDay->update(['is_current' => false]);

                    $operday = new OperDayModel;
                    $operday->operday = $data['operday'];
                    $operday->closed = false;
                    $operday->is_current = true;
                    $operday->created_by = auth()->id();
                    $operday->updated_by = auth()->id();
                    if ($operday->save()) {
                        Notification::make('operday-closed')
                            ->body(__("Опердень {$operday->operday->format('d.m.Y')} был успешно открыт"))
                            ->success()
                            ->send();
                    }
                })
                ->modalButton(__('Открыт опердень'))
                ->modalSubheading('Вы уверены, что хотели бы открыт опердень? Этого нельзя отменить.')
                ->modalWidth('sm')
                ->color('secondary')
                ->after(fn ($livewire) => $livewire->emit('refresh-operday-widget')),
            // Close operday
            Action::make('close-operday')
                ->hidden($this->currentOperDay->closed)
                ->label(__('Закрыт опердень'))
                ->mountUsing(fn (ComponentContainer $form) => $form->fill([
                    'operDayId' => $this->currentOperDay->id,
                ]))
                ->action(function (array $data) {
                    $operDay = OperDayModel::query()->find($data['operDayId']);
                    $operDay->closed = true;
                    if ($operDay->save()) {
                        $this->currentOperDay = $operDay;
                        Notification::make('operday-closed')
                            ->body(__("Опердень {$operDay->operday->format('d.m.Y')} был успешно закрыт"))
                            ->success()
                            ->send();
                    }
                })
                ->form([
                    Placeholder::make(__('Опердень'))
                        ->content($this->currentOperDay->operday->format('d.m.Y')),
                    Hidden::make('operDayId')->required()
                ])
                ->modalButton(__('Закрыт опердень'))
                ->modalSubheading('Вы уверены, что хотели бы закрыт опердень? Этого нельзя отменить.')
                ->modalWidth('sm')
                ->color('danger')
                ->after(fn ($livewire) => $livewire->emit('refresh-operday-widget'))
        ];
    }

    public function refreshOperDay()
    {
        $currentOperDay = OperDayModel::query()->whereIsCurrent(true)->first();

        $this->currentOperDay = $currentOperDay;
    }
}
