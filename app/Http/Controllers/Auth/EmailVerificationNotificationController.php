<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            EmailVerificationPromptController::redirect();
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with(
            'status',
            [
                'title' => trans('verification.title'),
                'icon' => '📩',
                'paragraph' => trans('verification.paragraph')
            ]
        );
    }
}
