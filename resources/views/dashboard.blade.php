<x-app-layout>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Dasboard Page') }}
    </x-slot>

    <x-slot name="header">
        <h1 class="text-lg font-semibold leading-tight text-gray-800 sm:text-lg md:text-2xl">
            {{ __('Dashboard') }}
        </h1>
    </x-slot>


    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>