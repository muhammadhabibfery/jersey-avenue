<x-app-layout>
    <x-slot name="title">
        {{ config('app.name') }} - {{ __('Profile Page') }}
    </x-slot>

    <x-slot name="header">
        <x-text-header class="mt-[73px] cursor-default">
            {{ __('Profile') }}
        </x-text-header>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <livewire:profile />
        </div>
    </div>
</x-app-layout>
