<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $route = route('login');

        if ($request->user()->hasVerifiedEmail())
            return EmailVerificationPromptController::redirect();

        if ($request->user()->markEmailAsVerified())
            event(new Verified($request->user()));

        if (auth()->check()) {
            $route = session()->has('cartRoute')
                ? session()->pull('cartRoute')
                : route('home');

            $paragraph = null;
        } else {
            $paragraph = trans('verification.verified-login');
        }

        return redirect($route)->with(
            'status',
            [
                'title' => trans('verification.verified'),
                'paragraph' => $paragraph
            ]
        );
    }
}
