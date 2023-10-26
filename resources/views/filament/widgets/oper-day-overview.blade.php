<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold">{{__('Текущий опердень')}}</h2>
        <div class="mt-4 text-lg" class="flex items-center space-x-2">
            <span>{{ $this->currentOperDay->operday->format('d.m.Y') }}</span>
            <span
                class="px-2 py-1 rounded-xl font-bold bg-{{ $this->currentOperDay->closed ? 'danger' : 'success'}}-500/10 text-{{ $this->currentOperDay->closed ? 'danger' : 'success'}}-700">{{
                $this->currentOperDay->closed ?
                __('Закрыт') : 'Открыт'}}</span>
        </div>
    </x-filament::card>
</x-filament::widget>