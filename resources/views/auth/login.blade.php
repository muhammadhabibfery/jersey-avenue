<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="py-2 mb-3 text-sm text-white md:text-2xl">
            {{ __('Login') }}
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-white">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="block mt-4">
            <x-primary-button class="justify-center w-full text-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="w-full mt-4 text-center md:flex md:justify-between">
            <div class="md:w-6/12">
                <a class="text-sm text-white underline rounded-md hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('register') }}">
                    {{ __("Doesn't have an account? Register") }}
                </a>
            </div>
            <div class="mt-3 md:w-6/12 md:flex md:justify-end md:items-center md:mt-0">
                @if (Route::has('password.request'))
                <a class="text-sm text-white underline rounded-md hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
            </div>
        </div>
    </form>
</x-guest-layout>
