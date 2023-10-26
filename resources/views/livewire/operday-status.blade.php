<div>
    <div class="flex items-center space-x-2 rounded-xl">
        <span>{{ __('Опердень:')}} {{ $this->operday->operday->format('d.m.Y') }}</span>
        <span
            class="px-2 py-1 rounded-xl font-bold text-sm bg-{{ $this->operday->closed ? 'danger' : 'success'}}-500/10 text-{{ $this->operday->closed ? 'danger' : 'success'}}-500">{{
            $this->operday->closed ?
            __('Закрыт') : 'Открыт'}}</span>
    </div>
</div>