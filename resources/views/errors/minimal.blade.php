<x-app-layout>
    <x-slot name="title">
        @yield('title')
    </x-slot>


    <div class="py-12">
        <div class="container mx-auto space-y-6 flex min-h-[100vmin] md:min-h-[80vmin]">
            <div class="m-auto text-center px-5">
                <div class="flex">
                    <h3
                        class="pr-5 font-serif font-semibold text-3xl md:text-4xl lg:text-5xl text-gray-500 border-r-2 border-gray-400">
                        @yield('code')
                    </h3>
                    <h3 class="pl-5 font-serif font-semibold text-2xl md:text-3xl lg:text-4xl pt-1 text-gray-500">
                        @yield('message')
                    </h3>
                </div>

                <x-primary-link href="{{ route('home') }}" class="mt-6 text-sm text-white bg-gray-700 rounded-md">
                    {{ __('Back to home') }}
                </x-primary-link>
            </div>
        </div>
    </div>
</x-app-layout>