<x-guest-layout>
    <x-slot:title>
        {{ config('app.name', 'Laravel') }} - {{ __('Verify Email Page') }}
    </x-slot:title>

    <div class="py-2 mb-3 text-sm text-white md:text-2xl">
        {{ __('Verification Email Address') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" />

    <div class="mb-4 text-xs text-slate-400 md:text-lg">
        {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
    </div>

        <div class="w-full mt-4 text-center md:flex md:justify-between md:mt-5">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-primary-button class="text-xs md:text-sm">
                        {{ __('Resend Verification Email') }}
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <div class="mt-3  md:flex md:justify-end md:items-center md:mt-2">
                    <button type="submit"
                        class="underline text-sm text-white hover:text-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Log Out') }}
                    </button>
                </div>
            </form>
        </div>
</x-guest-layout>
