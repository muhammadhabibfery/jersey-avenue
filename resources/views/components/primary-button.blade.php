@props(['livewireButton', 'request'])

<span x-data="{open: false}">
    @if (isset($livewireButton))
    <button wire:loading.attr="disabled" wire:loading.class="cursor-not-allowed hover:bg-gray-600 focus:bg-gray-600" {{
        $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2
        bg-gray-800 border
        border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700
        focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
        transition ease-in-out duration-150']) }}>
        <span wire:loading>{{ isset($request) ? $slot : __('Please wait ...') }}</span>
        <span wire:loading.class="hidden">{{ $slot }}</span>
    </button>
    @else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2
        bg-gray-800 border
        border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700
        focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
        transition ease-in-out duration-150']) }}
        @click="open=true"
        :class="open ? 'disabled hover:bg-gray-600 focus:bg-gray-600 cursor-not-allowed' : ''"
        x-text="open ? '{{ __('Please wait ...') }}' : '{{ $slot }}'">
    </button>
    @endif
</span>