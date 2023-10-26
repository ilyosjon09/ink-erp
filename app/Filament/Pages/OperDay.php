<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OperDayOverview;
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

    public function mount(): void
    {
        $currentOperDay = OperDayModel::query()->when(
            OperDayModel::query()->where('closed', false)->count() === 1,
            callback: fn ($query) => $query->where('closed', false),
            default: fn ($query) => $query->orderBy('operday', 'desc')
        )->first();

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
                ->form([
                    DatePicker::make('operday')
                        ->label(__('Опердень'))
                ])
                ->action(function ($data) {
                })
                ->modalButton(__('Открыт опердень'))
                ->modalSubheading('Вы уверены, что хотели бы открыт опердень? Этого нельзя отменить.')
                ->modalWidth('sm')
                ->color('secondary'),
            // Close operday
            Action::make('close-operday')
                ->label(__('Закрыт опердень'))
                ->mountUsing(fn (ComponentContainer $form) => $form->fill([
                    'operDayId' => $this->currentOperDay->id,
                ]))
                ->action(function (array $data) {
                    $operDay = OperDayModel::query()->find($data['operDayId']);
                    $operDay->closed = true;
                    if ($operDay->save()) {
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
        ];
    }
}
