<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OperDayOverview extends Widget
{
    protected static string $view = 'filament.widgets.oper-day-overview';
    public $currentOperDay;

    public function mount($currentOperDay): void
    {
        $this->currentOperDay = $currentOperDay;
    }
}
