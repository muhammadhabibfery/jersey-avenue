<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

trait GoogleAuth
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')
            ->user();

        $user = User::firstOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'role' => User::$roles[2],
                'password' => Hash::make('abc@123123'),
                'email_verified_at' => now(config('app.timezone'))
            ]
        );

        Auth::login($user);

        $route = checkSessionRoute();
        return redirect($route);
    }
}
