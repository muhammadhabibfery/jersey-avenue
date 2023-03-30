<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:90'],
            'username' => ['required', 'string', 'alpha_dash', 'max:35', 'unique:' . User::class],
            'email' => ['required', 'string', 'email', 'max:35', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::min(10)->numbers()->symbols()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => User::$roles[2],
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return to_route('login')
            ->with(
                'status',
                [
                    'title' => trans('auth.registered.title'),
                    'icon' => '📩',
                    'paragraph' => trans('auth.registered.paragraph')
                ]
            );
    }
}
