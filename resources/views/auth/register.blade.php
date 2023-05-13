<x-guest-layout>
    <x-slot name="title">
        {{ config('app.name', 'Laravel') }} - {{ __('Register Page') }}
    </x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="py-2 mb-3 text-sm text-white md:text-2xl">
            {{ __('Register') }}
        </div>

        <!-- Social Authentication -->
        <x-social-auth description="Register"></x-social-auth>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required
                autocomplete="name" />
            <x-input-error :messages="$errors->get('name')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('username')" />
            <x-text-input id="username" class="block w-full mt-1" type="text" name="username" :value="old('username')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')"
                class="p-1 pl-2 mt-2 border border-red-500 rounded-md bg-slate-200/75" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
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

        <div class="block mt-4">
            <x-primary-button class="justify-center w-full text-center">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <div class="flex items-center justify-center mt-4">
            <a class="text-sm text-white underline rounded-md hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered? Login') }}
            </a>
        </div>
    </form>
</x-guest-layout>