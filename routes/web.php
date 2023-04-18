<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;

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

Route::post('/checkout/payment/notification/', [CheckoutController::class, 'notificationHandler'])
    ->name('checkout.payment.notification');

Route::middleware('auth')
    ->group(function () {
        Route::view('/profile', 'profile.edit')
            ->name('profile.edit');

        Route::middleware('verified')
            ->group(function () {
                Route::view('/dashboard', 'dashboard')
                    ->name('dashboard');

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
