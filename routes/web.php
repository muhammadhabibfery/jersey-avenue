<?php

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Notifications\OrderNotification;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SocialiteController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/auth/google", [SocialiteController::class, "redirectToGoogle"])->name("auth.redirect.google");
Route::get("/auth/google/callback", [SocialiteController::class, "handleGoogleCallback"])->name("auth.callback.google");
Route::get('/admin/login', fn (): RedirectResponse => to_route('login'))
    ->name('filament.auth.login');
Route::post('/checkout/payment/notification/', [CheckoutController::class, 'notificationHandler'])
    ->name('checkout.payment.notification');
Route::post('/mark-as-read-order-notification', function (Request $request): RedirectResponse {
    $response = ['code' => 500, 'message' => 'Failed'];

    if ($request->has('message')) {
        $employees = getEmployees();
        $employees->each(function (User $employee) use ($request): void {
            $employee->notifications
                ->where('type', OrderNotification::class)
                ->where('data.body', $request->json('message'))
                ->markAsRead();
        });
        $response = ['code' => 200, 'message' => 'Success'];
    }

    return response()->json($response);
})
    ->name('mark-notification');

Route::middleware('auth')
    ->group(function () {
        Route::view('/profile', 'profile.edit')
            ->name('profile.edit');

        Route::middleware('verified')
            ->group(function () {
                Route::get("checkout/payment/finish", [CheckoutController::class, "finish"])->name("checkout.payment.finish");
                Route::get("checkout/payment/unfinish", [CheckoutController::class, "unfinish"])->name("checkout.payment.unfinish");
                Route::get("checkout/payment/error", [CheckoutController::class, "error"])->name("checkout.payment.error");
                Route::get('/checkout/success/', [CheckoutController::class, 'success'])->name('checkout.success');
                Route::get('/checkout/pending/', [CheckoutController::class, 'pending'])->name('checkout.pending');
                Route::get('/checkout/failed/', [CheckoutController::class, 'failed'])->name('checkout.failed');
            });
    });

$routes = glob(__DIR__ . '/extends/*.php');
foreach ($routes as $route) require($route);
