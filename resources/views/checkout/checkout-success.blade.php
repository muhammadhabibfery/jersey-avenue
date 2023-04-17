<x-app-layout>
    <x-slot name="title">
        {{ config('app.name') }} - {{ __('Transaction Success') }}
    </x-slot>

    <div class="py-12">
        <div class="container flex px-2 pt-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="w-full md:w-[55%] m-auto">
                <div class="box-content p-2 shadow-md shadow-stone-500 text-center">
                    <img src="{{ asset('assets/images/checkout/mailbox.jpg') }}" alt="Success"
                        class="object-cover bg-danger-600 mx-auto w-[75%] md:w-[75%] lg:w-[45%]">
                    <div class="leading-7 pt-2">
                        <h1 class="font-semibold text-lg md:text-xl lg:text-3xl">{{ __('Yay! Success') }}</h1>
                        <p class="font-mono text-sm lg:text-lg">{{ __('Thank you for completing the transaction') }}</p>
                        <p class="font-mono text-sm lg:text-base">{{ __('We\'ve sent you an email for detail
                            transaction')
                            }}</p>
                    </div>
                    <x-primary-link href="{{ route('home') }}" class="mt-3 text-sm text-white bg-gray-700 rounded-md">
                        {{ __('Back to home') }}
                    </x-primary-link>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
