<?php

namespace App\Http\Livewire;

use App\Models\OperDay;
use Livewire\Component;

class OperdayStatus extends Component
{
    public $operday;
    protected $listeners = ['refresh-operday-widget' => 'refreshOperDay'];

    public function mount()
    {
        $this->operday = OperDay::query()->firstWhere('is_current', '=', true);
    }

    public function render()
    {
        return view('livewire.operday-status');
    }

    public function refreshOperDay()
    {
        $this->operday = OperDay::query()->firstWhere('is_current', '=', true);
    }
}
