<x-filament::page>
    <div x-data="{visible: false, url: null}"
        @open-image-preview-modal.window="visible = true; url = $event.detail.url">
        <template x-teleport="body">
            <div x-show="visible" x-transition.opacity.duration.300ms @click="visible = false"
                class="absolute flex items-center justify-center p-8 z-50 bg-black/50 inset-0">
                <div class="flex">
                    <div>
                        <img x-bind:src="url" alt="Order image" style="max-height:80vh; width:auto;">
                    </div>
                    <div class="px-2">
                        <button class="text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
    <div>
        <div class="pb-4">
            <h2 class="text-lg">{{ __('Заказы')}}</h2>
        </div>
        <livewire:printing-shop.orders />
    </div>
</x-filament::page>