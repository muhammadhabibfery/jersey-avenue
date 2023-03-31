<x-guest-layout>
    <x-slot:title>
        {{ config('app.name', 'Laravel') }} - {{ __('Login Page') }}
    </x-slot:title>

    <div class="mb-4 text-xs text-slate-300 md:text-lg">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a pass word reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <div class="block mt-4">
            <x-primary-button class="justify-center w-full text-center">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>

        <div class="flex items-center justify-center mt-4">
            <a class="text-sm text-white underline rounded-md hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Back to login') }}
            </a>
        </div>

    </form>
</x-guest-layout>
