<x-app-layout>
    <x-slot name="title">
        {{ config('app.name') }} - {{ __('Profile Page') }}
    </x-slot>

    <x-slot name="header">
        <h1 class="text-lg font-semibold leading-tight text-gray-800 sm:text-lg md:text-2xl">
            {{ __('Profile') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            <livewire:profile />
        </div>
    </div>
</x-app-layout>