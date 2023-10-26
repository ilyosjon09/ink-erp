<?php

namespace App\Filament\Pages\Widgets;

use App\Models\OperDay;
use Filament\Widgets\Widget;

class OperDayOverview extends Widget
{
    protected static string $view = 'filament.widgets.oper-day-overview';
    public OperDay $currentOperDay;
    protected $listeners = ['refresh-operday-widget' => 'refreshOperDay'];

    public function mount(OperDay $currentOperDay): void
    {
        $this->currentOperDay = $currentOperDay;
    }

    public function refreshOperDay()
    {
        $currentOperDay = OperDay::query()->whereIsCurrent(true)->first();

        $this->currentOperDay = $currentOperDay;
    }
}
