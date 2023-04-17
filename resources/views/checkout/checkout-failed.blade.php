<x-app-layout>
    <x-slot name="title">
        {{ config('app.name') }} - {{ __('Transaction Failed') }}
    </x-slot>

    <div class="py-12">
        <div class="container flex px-2 pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="w-full md:w-[55%] m-auto">
                <div class="box-content p-2 shadow-md shadow-stone-500 text-center">
                    <img src="{{ asset('assets/images/checkout/checkout-failed.png') }}" alt="Success"
                        class="object-cover bg-danger-600 mx-auto w-[55%] lg:w-[45%]">
                    <div class="leading-7 pt-2">
                        <h1 class="font-semibold text-lg md:text-xl lg:text-3xl">{{ __('Sorry ...') }}</h1>
                        <p class="font-mono text-sm lg:text-lg">{{ __('Something went wrong') }}</p>
                        <p class="font-mono text-sm lg:text-base">{{ __('We couldn\'t process your transaction') }}</p>
                    </div>
                    <x-primary-link href="{{ route('home') }}" class="mt-3 text-sm text-white bg-gray-700 rounded-md">
                        {{ __('Back to home') }}
                    </x-primary-link>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
