<x-guest-layout>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Login Page') }}
    </x-slot>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <div class="py-2 mb-3 text-sm text-white md:text-2xl">
            {{ __('Reset Password') }}
        </div>

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email"
                :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block w-full mt-1" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>