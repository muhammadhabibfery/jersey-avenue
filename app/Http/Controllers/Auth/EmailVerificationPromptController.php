<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
            ?  self::redirect()
            : view('auth.verify-email');
    }

    /**
     * Redirect if user has verified email.
     */
    public static function redirect(): RedirectResponse
    {
        if (auth()->check()) {
            return url()->previous() != config('app.url') . '/verify-email'
                ? back()
                : redirect()->intended(RouteServiceProvider::HOME);
        }

        return to_route('login');
    }
}
